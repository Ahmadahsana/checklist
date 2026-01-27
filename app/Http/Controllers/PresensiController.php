<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    // public function index()
    // {
    //     $kegiatans = Kegiatan::withCount(['presensis' => function ($query) {
    //         $query->where('hadir', true);
    //     }])->withCount('peserta')->get();
    //
    //     $chartData = $this->prepareChartData($kegiatans);
    //
    //     return view('presensi.index', compact('kegiatans', 'chartData'));
    // }

    public function index()
    {
        // Hanya user biasa yang bisa mengakses
        if (auth()->user()->role !== 'user') {
            abort(403, 'Unauthorized');
        }

        $today = now();
        $dayName = $today->isoFormat('dddd');

        // Update query untuk mengakomodir rutin (hari) dan insidental (tanggal)
        $kegiatan = Kegiatan::where(function ($q) use ($today, $dayName) {
            $q->whereDate('tanggal', '>=', $today->toDateString())
                ->orWhere('hari', $dayName);
        })->get();

        $currentTime = now();

        // Cari kegiatan yang sedang aktif (hari ini dan dalam rentang waktu)
        $kegiatanAktif = Kegiatan::where(function ($q) use ($today, $dayName) {
            $q->whereDate('tanggal', $today->toDateString())
                ->orWhere('hari', $dayName);
        })
            ->where('jam_mulai', '<=', $currentTime->format('H:i:s'))
            ->where('jam_selesai', '>=', $currentTime->format('H:i:s'))
            ->first();

        return view('presensi.personal', compact('kegiatan', 'kegiatanAktif'));
    }

    protected function prepareChartData($kegiatans)
    {
        $categories = $kegiatans->pluck('nama_kegiatan')->toArray();
        $series = [
            [
                'name' => 'Persentase Kehadiran',
                'data' => $kegiatans->map(function ($kegiatan) {
                    $totalPeserta = $kegiatan->peserta_count;
                    $hadirCount = $kegiatan->presensis_count;
                    return $totalPeserta > 0 ? ($hadirCount / $totalPeserta) * 100 : 0;
                })->toArray(),
            ]
        ];

        return [
            'categories' => $categories,
            'series' => $series,
        ];
    }


    public function submit(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'kode_unik' => 'required|numeric',
        ]);

        $kegiatan = Kegiatan::findOrFail($request->kegiatan_id);

        // Ambil waktu saat ini
        $currentTime = now();

        // Tentukan tanggal referensi untuk validasi waktu
        // Jika insidental -> pakai tanggal kegiatan
        // Jika rutin -> pakai hari ini (asumsi user absen di hari yang sama)
        $referenceDate = $kegiatan->jenis === 'rutin' ? now()->toDateString() : $kegiatan->tanggal;

        // Gabungkan tanggal kegiatan dengan jam_mulai dan jam_selesai untuk perbandingan
        $startTime = $referenceDate . ' ' . $kegiatan->jam_mulai;
        $endTime = $referenceDate . ' ' . $kegiatan->jam_selesai;

        // Konversi ke objek Carbon untuk perbandingan
        $startDateTime = \Carbon\Carbon::parse($startTime);
        $endDateTime = \Carbon\Carbon::parse($endTime);

        // Cek apakah waktu saat ini berada dalam rentang waktu kegiatan
        if ($currentTime->lt($startDateTime)) {
            return redirect()->back()->with('error', 'Presensi tidak valid karena waktu belum dimulai.');
        }

        if ($currentTime->gt($endDateTime)) {
            return redirect()->back()->with('error', 'Presensi tidak valid karena waktu sudah berakhir.');
        }

        // Cek apakah kode presensi benar
        // Gunakan kode_mingguan (aksesor di model yang handle logika rutin vs insidental)
        if ($kegiatan->kode_mingguan != $request->kode_unik) {
            return redirect()->back()->with('error', 'Kode presensi salah.');
        }

        // Cek apakah user sudah presensi
        $presensi = Presensi::where('kegiatan_id', $kegiatan->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($presensi) {
            return redirect()->back()->with('error', 'Anda sudah melakukan presensi untuk kegiatan ini.');
        }

        // Simpan presensi
        Presensi::create([
            'user_id' => Auth::user()->id,
            'kegiatan_id' => $kegiatan->id,
            'hadir' => 1,
            'kode_masuk' => $request->kode_unik,
            'jam_hadir' => date('H:i:s'),
            'keterangan' => 'valid', // valid dan tidak valid
        ]);

        return redirect()->back()->with('success', 'Presensi berhasil disimpan.');
    }

    public function riwayat()
    {
        if (auth()->user()->role !== 'user') {
            abort(403, 'Unauthorized');
        }

        // Ambil semua riwayat presensi user (tanpa filter tanggal)
        $presensi = Presensi::where('user_id', Auth::user()->id)
            ->with('kegiatan')
            ->get();

        return view('presensi.riwayat', compact('presensi'));
    }
}
