<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\User;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatan = Kegiatan::latest()->get();
        return view('presensi.index', compact('kegiatan'));
    }

    public function create()
    {
        return view('presensi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'tipe' => 'required|in:rutin,insidental',
        ]);

        // Generate kode presensi (6 digit angka)
        $kodePresensi = mt_rand(100000, 999999);

        Kegiatan::create([
            'nama_kegiatan' => $request->nama_kegiatan,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_akhir,
            'tipe' => $request->tipe,
            'kode_unik' => $kodePresensi,
        ]);

        return redirect()->route('presensi.index')->with('success', 'Kegiatan berhasil dibuat. Kode presensi: ' . $kodePresensi);
    }

    public function show(Kegiatan $kegiatan)
    {
        $presensis = $kegiatan->presensis()->with('user')->get();
        $totalPeserta = User::where('role', 'user')->count();
        $hadir = $presensis->where('hadir', true)->count();
        $tidakHadir = max(0, $totalPeserta - $hadir);
        $persentaseKehadiran = $totalPeserta > 0 ? round(($hadir / $totalPeserta) * 100, 2) : 0;
        $persentaseKetidakhadiran = 100 - $persentaseKehadiran;
        $presensiTidakHadir = $presensis->where('hadir', false)->values();
        $belumHadirUsers = User::where('role', 'user')
            ->whereDoesntHave('presensis', function ($query) use ($kegiatan) {
                $query->where('kegiatan_id', $kegiatan->id);
            })->get();

        return view('presensi.show', [
            'kegiatan' => $kegiatan,
            'presensis' => $presensis,
            'totalPeserta' => $totalPeserta,
            'hadir' => $hadir,
            'tidakHadir' => $tidakHadir,
            'persentaseKehadiran' => $persentaseKehadiran,
            'persentaseKetidakhadiran' => $persentaseKetidakhadiran,
            'presensiTidakHadir' => $presensiTidakHadir,
            'belumHadirUsers' => $belumHadirUsers,
        ]);
    }
}
