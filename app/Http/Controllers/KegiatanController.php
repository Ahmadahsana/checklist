<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\User;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
            'tipe' => 'required|in:rutin,insidental',
            'jam_mulai' => 'required',
            'jam_akhir' => 'required',
            'tanggal' => 'required_if:tipe,insidental|nullable|date',
            'hari' => 'required_if:tipe,rutin|nullable|string',
        ]);

        // Generate kode presensi (6 digit angka)
        $kodePresensi = mt_rand(100000, 999999);

        Kegiatan::create([
            'nama_kegiatan' => $request->nama_kegiatan,
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_akhir,
            'jenis' => $request->tipe, // Map tipe form -> jenis database
            'kode_unik' => $kodePresensi,
        ]);

        return redirect()->route('presensi.index')->with('success', 'Kegiatan berhasil dibuat. Kode presensi: ' . $kodePresensi);
    }

    public function destroy(Kegiatan $kegiatan)
    {
        // Hapus presensi terkait terlebih dahulu
        $kegiatan->presensis()->delete();
        $kegiatan->delete();

        return redirect()->route('presensi.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function show(Request $request, Kegiatan $kegiatan)
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

        $weeklyRecap = [];
        $weeklyHeaders = [];
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        if ($kegiatan->jenis === 'rutin') {
            // Mapping nama hari Indonesia ke Carbon ID
            $daysMap = [
                'Minggu' => Carbon::SUNDAY,
                'Senin' => Carbon::MONDAY,
                'Selasa' => Carbon::TUESDAY,
                'Rabu' => Carbon::WEDNESDAY,
                'Kamis' => Carbon::THURSDAY,
                'Jumat' => Carbon::FRIDAY,
                'Sabtu' => Carbon::SATURDAY,
            ];

            $targetDay = $daysMap[$kegiatan->hari] ?? null;
            $dates = [];

            if ($targetDay !== null) {
                // Generate semua tanggal di bulan & tahun terpilih yang harinya sesuai
                $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
                $endDate = $startDate->copy()->endOfMonth();

                // Cari tanggal pertama yang sesuai harinya
                if ($startDate->dayOfWeek !== $targetDay) {
                    $startDate->next($targetDay);
                }

                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addWeek();
                }
            }

            // Siapkan Header (Minggu 1, Minggu 2, dst) dengan format tanggal
            $weeklyHeaders = array_map(function ($date, $idx) {
                return 'Minggu ' . ($idx + 1) . ' (' . $date->format('d/m') . ')';
            }, $dates, array_keys($dates));

            // Simpan tanggal asli untuk key pencarian presensi
            $targetDates = $dates;

            $users = User::where('role', 'user')->get();

            // Ambil semua presensi untuk kegiatan ini di bulan tsb (berdasarkan created_at)
            $monthPresensis = $kegiatan->presensis()
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth)
                ->get();

            foreach ($users as $user) {
                $statuses = [];
                foreach ($targetDates as $date) {
                    // Cek apakh user absen pada tanggal ini (cocokkan Y-m-d)
                    $p = $monthPresensis->filter(function ($item) use ($user, $date) {
                        return $item->user_id == $user->id &&
                            Carbon::parse($item->created_at)->isSameDay($date);
                    })->first();

                    if ($p && $p->hadir) {
                        $statuses[] = '✔';
                    } elseif ($date->isFuture()) { // Jika tanggal belum lewat
                        $statuses[] = '-';
                    } else { // Jika tanggal sudah lewat dan tidak ada presensi
                        $statuses[] = '✘';
                    }
                }
                $weeklyRecap[] = [
                    'user' => $user,
                    'statuses' => $statuses,
                ];
            }
        }

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
            'weeklyRecap' => $weeklyRecap,
            'weeklyHeaders' => $weeklyHeaders,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
        ]);
    }
    public function exportCsv(Request $request, Kegiatan $kegiatan)
    {
        if ($kegiatan->jenis !== 'rutin') {
            return redirect()->back()->with('error', 'Export CSV hanya untuk kegiatan rutin.');
        }

        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        // Logic yang sama dengan show() untuk mendapatkan tanggal-tanggal kegiatan
        $daysMap = [
            'Minggu' => Carbon::SUNDAY,
            'Senin' => Carbon::MONDAY,
            'Selasa' => Carbon::TUESDAY,
            'Rabu' => Carbon::WEDNESDAY,
            'Kamis' => Carbon::THURSDAY,
            'Jumat' => Carbon::FRIDAY,
            'Sabtu' => Carbon::SATURDAY,
        ];

        $targetDay = $daysMap[$kegiatan->hari] ?? null;
        $dates = [];

        if ($targetDay !== null) {
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
            $endDate = $startDate->copy()->endOfMonth();

            if ($startDate->dayOfWeek !== $targetDay) {
                $startDate->next($targetDay);
            }

            while ($startDate->lte($endDate)) {
                $dates[] = $startDate->copy();
                $startDate->addWeek();
            }
        }

        // Siapkan nama file
        $fileName = 'Rekap_Presensi_' . Str::slug($kegiatan->nama_kegiatan) . '_' . $selectedYear . '_' . $selectedMonth . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array_merge(['No', 'Nama Peserta'], array_map(function ($date, $idx) {
            return 'Minggu ' . ($idx + 1) . ' (' . $date->format('d/m') . ')';
        }, $dates, array_keys($dates)));

        // Tambahkan kolom Total Hadir & Persentase
        $columns[] = 'Total Hadir';
        $columns[] = 'Persentase';

        $users = User::where('role', 'user')->get();
        $monthPresensis = $kegiatan->presensis()
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->get();

        $callback = function () use ($users, $dates, $monthPresensis, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $index => $user) {
                $row = [$index + 1, $user->nama_lengkap ?? $user->username];
                $hadirCount = 0;
                $totalWeeks = count($dates);

                foreach ($dates as $date) {
                    $p = $monthPresensis->filter(function ($item) use ($user, $date) {
                        return $item->user_id == $user->id &&
                            Carbon::parse($item->created_at)->isSameDay($date);
                    })->first();

                    if ($p && $p->hadir) {
                        $row[] = 'Hadir';
                        $hadirCount++;
                    } else {
                        // Cek apakah tanggal sudah lewat
                        $row[] = $date->isFuture() ? '-' : 'Tidak Hadir';
                    }
                }

                $row[] = $hadirCount;
                $row[] = $totalWeeks > 0 ? round(($hadirCount / $totalWeeks) * 100) . '%' : '0%';

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
