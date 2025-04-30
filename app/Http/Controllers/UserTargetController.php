<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\UserTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTargetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // if (!Gate::allows('is-user')) {
        //     abort(403, 'Akses ditolak. Hanya user biasa yang dapat mengakses halaman ini.');
        // }

        $user = Auth::user();
        $programs = Program::whereIn('level', [$user->level, 'both'])->get();
        $sevenDaysAgo = now()->subDays(7)->startOfDay();

        // Ambil tanggal dari query string (GET) atau hari ini, tanpa validasi wajib
        $selectedDate = $request->query('date', now()->toDateString());

        // Validasi sederhana untuk memastikan tanggal valid
        if (!$this->isValidDate($selectedDate, $sevenDaysAgo)) {
            $selectedDate = now()->toDateString(); // Kembalikan ke hari ini jika invalid
        }

        // Ambil target user untuk tanggal yang dipilih, pastikan hanya target yang benar-benar ada dan completed
        $userTargets = UserTarget::where('user_id', $user->id)
            ->where('date', $selectedDate)
            ->where('status', 'completed') // Pastikan hanya target yang completed
            ->with('program')
            ->get()
            ->keyBy('program_id'); // Index berdasarkan program_id

        // Debugging: Log data untuk memastikan semua target diterima
        // \Log::info('User Targets for date ' . $selectedDate . ' (GET)', $userTargets->toArray());

        return view('user-targets.index', compact('programs', 'userTargets', 'selectedDate'));
    }

    public function store(Request $request)
    {
        // if (!Gate::allows('is-user')) {
        //     abort(403, 'Akses ditolak. Hanya user biasa yang dapat mengakses halaman ini.');
        // }

        $user = Auth::user();
        $date = $request->input('date');

        // Debugging: Log data yang diterima dari form
        // \Log::info('Received target data for date ' . $date, $request->all());

        $request->validate([
            'date' => 'required|date|before_or_equal:' . now()->toDateString() . '|after_or_equal:' . now()->subDays(7)->toDateString(),
            'targets' => 'required|array',
            'targets.*.program_id' => 'required|exists:programs,id',
            'targets.*.value' => [
                'required',
                function ($attribute, $value, $fail) {
                    $programId = explode('.', $attribute)[1]; // Ambil program_id dari atribut
                    $program = Program::find($programId);
                    if (!$program) {
                        $fail('Program tidak ditemukan.');
                        return;
                    }
                    if ($program->type === 'numeric' && (!is_numeric($value) || $value < 0)) {
                        $fail('Nilai harus angka positif untuk target numeric.');
                    }
                    if ($program->type === 'boolean' && !in_array($value, [0, 1])) {
                        $fail('Nilai harus 0 (Tidak) atau 1 (Ya) untuk target boolean.');
                    }
                },
            ],
        ]);

        // Simpan semua target untuk tanggal yang dipilih
        foreach ($request->input('targets') as $programData) {
            $programId = $programData['program_id'];
            $value = $programData['value'];
            $program = Program::find($programId);

            $existingTarget = UserTarget::where('user_id', $user->id)
                ->where('program_id', $programId)
                ->where('date', $date)
                ->first();

            $valueToSave = $program->type === 'boolean' ? ($value === '1' ? 1 : 0) : $value;

            if ($existingTarget) {
                $existingTarget->update(['value' => $valueToSave, 'status' => 'completed']);
            } else {
                UserTarget::create([
                    'user_id' => $user->id,
                    'program_id' => $programId,
                    'date' => $date,
                    'value' => $valueToSave,
                    'status' => 'completed',
                ]);
            }
        }

        // Debugging: Log data yang disimpan
        // \Log::info('Targets saved for date ' . $date, $request->input('targets'));

        return redirect()->route('user-targets.index', ['date' => $date])->with('success', 'Semua target berhasil disimpan');
    }

    private function isValidDate($date, $minDate)
    {
        try {
            $parsedDate = \Carbon\Carbon::parse($date);
            $maxDate = now();
            return $parsedDate->between($minDate, $maxDate);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function show($programId)
    {
        // if (!Gate::allows('is-user')) {
        //     abort(403, 'Akses ditolak. Hanya user biasa yang dapat mengakses halaman ini.');
        // }

        $user = Auth::user();
        $program = Program::findOrFail($programId);
        $sevenDaysAgo = now()->subDays(7)->startOfDay();

        // Ambil riwayat target selama 7 hari terakhir
        $targets = UserTarget::where('user_id', $user->id)
            ->where('program_id', $programId)
            ->where('date', '>=', $sevenDaysAgo)
            ->with('program')
            ->orderBy('date', 'desc')
            ->get();

        // Hitung presentase keberhasilan untuk setiap hari
        $chartData = $this->prepareChartData($targets, $program);

        return view('user-targets.show', compact('program', 'targets', 'chartData'));
    }

    private function calculateSuccessPercentage($targets, $program)
    {
        if ($targets->isEmpty()) {
            return 0;
        }

        $totalDays = $targets->count();
        $successfulDays = 0;

        foreach ($targets as $target) {
            if ($program->type === 'boolean') {
                if ($target->value == 1) {
                    $successfulDays++;
                }
            } else { // numeric
                $targetValue = floatval($target->value);
                $programTarget = floatval($program->target);
                if ($targetValue > 0 && $programTarget > 0) {
                    $successfulDays += ($targetValue / $programTarget) * 100;
                }
            }
        }

        return $program->type === 'boolean' ? ($successfulDays / $totalDays) * 100 : ($successfulDays / $totalDays);
    }

    // private function prepareChartData($targets, $program)
    // {
    //     $sevenDaysAgo = now()->subDays(7)->startOfDay();
    //     $dates = [];
    //     $percentages = [];

    //     // Buat array tanggal selama 7 hari terakhir
    //     for ($i = 6; $i >= 0; $i--) {
    //         $date = now()->subDays($i)->format('Y-m-d');
    //         $dates[] = \Carbon\Carbon::parse($date)->translatedFormat('l, d F'); // Format: "Senin, 20 Feb"
    //         $targetForDay = $targets->firstWhere('date', $date);

    //         if ($program->type === 'boolean') {
    //             $percentages[] = $targetForDay && $targetForDay->value == 1 ? 100 : 0;
    //         } else { // numeric
    //             $percentages[] = $targetForDay ? ($targetForDay->value / $program->target) * 100 : 0;
    //         }
    //     }

    //     return [
    //         'categories' => $dates,
    //         'series' => [$percentages],
    //     ];
    // }

    private function prepareChartData($targets, $program, $days)
    {
        $dates = [];
        $percentages = [];

        $startDate = now()->subDays($days - 1)->startOfDay();
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = \Carbon\Carbon::parse($date)->translatedFormat('l, d F'); // Format: "Senin, 20 Feb"
            $targetForDay = $targets->firstWhere('date', $date);

            if ($program->type === 'boolean') {
                $percentages[] = $targetForDay && $targetForDay->value == 1 ? 100 : 0;
            } else { // numeric
                $percentages[] = $targetForDay ? ($targetForDay->value / $program->target) * 100 : 0;
            }
        }

        return [
            'categories' => $dates,
            'series' => [$percentages],
        ];
    }

    // public function personalProgress()
    // {
    //     // if (!Gate::allows('is-user')) {
    //     //     abort(403, 'Akses ditolak. Hanya user biasa yang dapat mengakses halaman ini.');
    //     // }

    //     $user = Auth::user();
    //     $programs = Program::whereIn('level', [$user->level, 'both'])->get();

    //     // Default: Pilih program pertama atau kosong jika tidak ada
    //     $selectedProgram = $programs->first();
    //     $period = 'weekly'; // Default: mingguan

    //     if ($selectedProgram) {
    //         $chartData = $this->prepareChartDataForProgram($user, $selectedProgram->id, $period);
    //     } else {
    //         $chartData = ['categories' => [], 'series' => [[]]];
    //     }

    //     return view('user-targets.personal-progress', compact('programs', 'selectedProgram', 'chartData', 'period'));
    // }

    public function personalProgress()
    {
        // if (!Gate::allows('is-user')) {
        //     abort(403, 'Akses ditolak. Hanya user biasa yang dapat mengakses halaman ini.');
        // }

        $user = Auth::user();
        $programs = Program::whereIn('level', [$user->level, 'both'])->get();

        // Default: Pilih program pertama atau kosong jika tidak ada
        $selectedProgram = $programs->first();
        $period = 'weekly'; // Default: mingguan

        $chartData = [];
        $overallStats = $this->calculateOverallStats($user, $period);

        if ($selectedProgram) {
            $chartData = $this->prepareChartDataForProgram($user, $selectedProgram->id, $period);
        } else {
            $chartData = ['categories' => [], 'series' => [[]]];
        }

        return view('user-targets.personal-progress', compact('programs', 'selectedProgram', 'chartData', 'period', 'overallStats'));
    }

    public function updatePersonalProgress(Request $request)
    {
        // if (!Gate::allows('is-user')) {
        //     return response()->json(['error' => 'Akses ditolak. Hanya user biasa yang dapat mengakses halaman ini.'], 403);
        // }

        $user = Auth::user();
        $programId = $request->input('program_id');
        $period = $request->input('period', 'weekly');

        $program = Program::find($programId);

        if (!$program) {
            return response()->json(['error' => 'Program tidak ditemukan.'], 404);
        }

        $chartData = $this->prepareChartDataForProgram($user, $programId, $period);

        return response()->json(['chartData' => $chartData]);
    }

    // private function prepareChartDataForProgram($user, $programId, $period)
    // {
    //     $program = Program::findOrFail($programId);
    //     $targets = UserTarget::where('user_id', $user->id)
    //         ->where('program_id', $programId)
    //         ->with('program');

    //     if ($period === 'weekly') {
    //         $startDate = now()->subDays(7)->startOfDay();
    //         $targets = $targets->where('date', '>=', $startDate)->orderBy('date', 'desc')->get();
    //         return $this->prepareChartData($targets, $program, 7);
    //     } else { // monthly
    //         $startDate = now()->subDays(30)->startOfDay();
    //         $targets = $targets->where('date', '>=', $startDate)->orderBy('date', 'desc')->get();
    //         return $this->prepareChartData($targets, $program, 30);
    //     }
    // }

    private function prepareChartDataForProgram($user, $programId, $period)
    {
        $program = Program::findOrFail($programId);
        $targets = UserTarget::where('user_id', $user->id)
            ->where('program_id', $programId)
            ->with('program');

        if ($period === 'weekly') {
            $startDate = now()->subDays(7)->startOfDay();
            $targets = $targets->where('date', '>=', $startDate)->orderBy('date', 'desc')->get();
            return $this->prepareChartData($targets, $program, 7);
        } else { // monthly
            $startDate = now()->subMonths(6)->startOfMonth(); // Ambil 6 bulan terakhir
            $targets = $targets->where('date', '>=', $startDate)->orderBy('date', 'desc')->get();
            return $this->prepareChartDataByMonth($targets, $program, 6); // Kirim jumlah bulan (6)
        }
    }

    public function dashboard()
    {
        // if (!Gate::allows('is-user')) {
        //     abort(403, 'Akses ditolak. Hanya user biasa yang dapat mengakses halaman ini.');
        // }

        $user = Auth::user();
        $today = now()->toDateString();
        $sevenDaysAgo = now()->subDays(7)->startOfDay();

        // Ambil semua program yang sesuai dengan level user
        $programs = Program::whereIn('level', [$user->level, 'both'])->get();

        // Hitung progress harian keseluruhan hari ini
        $dailyProgress = $this->calculateDailyProgress($user, $today, $programs);

        // Cek apakah ada target hari ini
        $hasTargetsToday = UserTarget::where('user_id', $user->id)
            ->where('date', $today)
            ->exists();

        // Data untuk chart (progress 7 hari terakhir untuk beberapa target)
        $chartData = $this->prepareOverallChartData($user, $programs, $sevenDaysAgo);

        return view('user-targets.dashboard', compact('dailyProgress', 'hasTargetsToday', 'chartData'));
    }

    private function calculateDailyProgress($user, $date, $programs)
    {
        $targets = UserTarget::where('user_id', $user->id)
            ->where('date', $date)
            ->with('program')
            ->get();

        if ($targets->isEmpty()) {
            return 0;
        }

        $totalPrograms = $programs->count();
        $totalProgress = 0;

        foreach ($targets as $target) {
            $program = $target->program;
            if ($program->type === 'boolean') {
                $totalProgress += $target->value == 1 ? 100 : 0;
            } else { // numeric
                $progress = ($target->value / $program->target) * 100;
                $totalProgress += $progress;
            }
        }

        return $totalPrograms > 0 ? round($totalProgress / $totalPrograms, 2) : 0;
    }

    // private function prepareOverallChartData($user, $programs, $startDate)
    // {
    //     $categories = [];
    //     $series = [];

    //     // Ambil 7 hari terakhir untuk weekly, atau 1 hari untuk daily
    //     $days = 7; // Default untuk weekly
    //     for ($i = $days - 1; $i >= 0; $i--) {
    //         $date = now()->subDays($i)->format('Y-m-d');
    //         $categories[] = \Carbon\Carbon::parse($date)->translatedFormat('d F'); // Format: "20 Feb"
    //     }

    //     // Ambil data untuk 3 program pertama (sesuai contoh Preline)
    //     $selectedPrograms = $programs->take(3); // Ambil 3 program untuk chart

    //     foreach ($selectedPrograms as $program) {
    //         $programTargets = UserTarget::where('user_id', $user->id)
    //             ->where('program_id', $program->id)
    //             ->where('date', '>=', $startDate)
    //             ->orderBy('date', 'asc')
    //             ->get()
    //             ->groupBy('date');

    //         $data = [];
    //         foreach ($categories as $category) {
    //             $date = \Carbon\Carbon::createFromFormat('d F', $category)->format('Y-m-d');
    //             $target = $programTargets->get($date, collect())->first();
    //             if ($program->type === 'boolean') {
    //                 $data[] = $target && $target->value == 1 ? 100 : 0;
    //             } else { // numeric
    //                 $data[] = $target ? ($target->value / $program->target) * 100 : 0;
    //             }
    //         }
    //         $series[] = [
    //             'name' => $program->nama_program,
    //             'data' => $data,
    //             'dashArray' => $selectedPrograms->keys()->search($program->id) === 2 ? 4 : 0 // Garis putus-putus untuk program ketiga (Others)
    //         ];
    //     }

    //     return [
    //         'categories' => $categories,
    //         'series' => $series,
    //     ];
    // }

    private function prepareOverallChartData($user, $programs, $startDate)
    {
        $categories = [];
        $series = [];

        // Ambil 7 hari terakhir untuk weekly
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $categories[] = $date; // Gunakan format numerik (YYYY-MM-DD)
        }

        // Ambil data untuk beberapa program (misalnya 3 program pertama)
        $selectedPrograms = $programs->take(3); // Ambil 3 program untuk chart

        foreach ($selectedPrograms as $program) {
            $programTargets = UserTarget::where('user_id', $user->id)
                ->where('program_id', $program->id)
                ->where('date', '>=', $startDate)
                ->orderBy('date', 'asc')
                ->get()
                ->groupBy('date');

            $data = [];
            foreach ($categories as $date) {
                $target = $programTargets->get($date, collect())->first();
                if ($program->type === 'boolean') {
                    $data[] = $target && $target->value == 1 ? 100 : 0;
                } else { // numeric
                    $data[] = $target ? ($target->value / $program->target) * 100 : 0;
                }
            }
            $series[] = [
                'name' => $program->nama_program,
                'data' => $data,
                'dashArray' => $selectedPrograms->keys()->search($program->id) === 2 ? 4 : 0 // Garis putus-putus untuk program ketiga
            ];
        }

        return [
            'categories' => $categories, // Kirim dalam format Y-m-d
            'series' => $series,
        ];
    }

    // public function updateDashboardChart(Request $request)
    // {
    //     // if (!Gate::allows('is-user')) {
    //     //     return response()->json(['error' => 'Akses ditolak. Hanya user biasa yang dapat mengakses halaman ini.'], 403);
    //     // }

    //     $user = Auth::user();
    //     $period = $request->input('period', 'weekly');
    //     $startDate = $period === 'weekly' ? now()->subDays(7)->startOfDay() : now()->startOfDay();
    //     $days = $period === 'weekly' ? 7 : 1;

    //     $programs = Program::whereIn('level', [$user->level, 'both'])->take(3)->get(); // Ambil 3 program untuk chart

    //     $categories = [];
    //     for ($i = $days - 1; $i >= 0; $i--) {
    //         $date = now()->subDays($i)->format('Y-m-d');
    //         $categories[] = \Carbon\Carbon::parse($date)->translatedFormat('d F'); // Format: "20 Feb"
    //     }

    //     $series = [];
    //     foreach ($programs as $program) {
    //         $programTargets = UserTarget::where('user_id', $user->id)
    //             ->where('program_id', $program->id)
    //             ->where('date', '>=', $startDate)
    //             ->orderBy('date', 'asc')
    //             ->get()
    //             ->groupBy('date');

    //         $data = [];
    //         foreach ($categories as $category) {
    //             $date = \Carbon\Carbon::createFromFormat('d F', $category)->format('Y-m-d');
    //             $target = $programTargets->get($date, collect())->first();
    //             if ($program->type === 'boolean') {
    //                 $data[] = $target && $target->value == 1 ? 100 : 0;
    //             } else { // numeric
    //                 $data[] = $target ? ($target->value / $program->target) * 100 : 0;
    //             }
    //         }
    //         $series[] = [
    //             'name' => $program->nama_program,
    //             'data' => $data,
    //             'dashArray' => $programs->keys()->search($program->id) === 2 ? 4 : 0 // Garis putus-putus untuk program ketiga
    //         ];
    //     }

    //     $chartData = [
    //         'categories' => $categories,
    //         'series' => $series,
    //     ];

    //     return response()->json(['chartData' => $chartData]);
    // }

    public function updateDashboardChart(Request $request)
    {
        try {


            $user = Auth::user();
            $period = $request->input('period', 'weekly'); // Default ke "weekly"

            $startDate = $period === 'weekly' ? now()->subDays(7)->startOfDay() : now()->subMonths(6)->startOfMonth(); // 6 bulan terakhir untuk bulanan

            $programs = Program::whereIn('level', [$user->level, 'both'])->take(3)->get(); // Ambil 3 program untuk chart

            $categories = [];
            $series = [];

            if ($period === 'weekly') {
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i)->format('Y-m-d');
                    $categories[] = $date; // Gunakan format numerik (YYYY-MM-DD) untuk mingguan
                }
            } else { // monthly (6 bulan terakhir)
                $categoriesData = $this->prepareChartDataByMonth($user, $programs, 6); // Gunakan logika dari Progress Personal
                $categories = $categoriesData['categories'];
            }

            foreach ($programs as $program) {
                $programTargets = UserTarget::where('user_id', $user->id)
                    ->where('program_id', $program->id)
                    ->where('date', '>=', $startDate)
                    ->orderBy('date', 'asc')
                    ->get()
                    ->groupBy('date');

                $data = [];
                foreach ($categories as $category) {
                    if ($period === 'weekly') {
                        $date = $category; // Format Y-m-d langsung digunakan
                        $target = $programTargets->get($date, collect())->first();
                        $data[] = $target ? ($target->value / $program->target) * 100 : 0;
                    } else { // monthly
                        $month = $category; // Nama bulan singkat (Sep, Oct, dll.)
                        $monthTargets = $programTargets->filter(function ($target) use ($month) {
                            if ($target instanceof App\Models\UserTarget) { // Pastikan namespace sesuai
                                return \Carbon\Carbon::parse($target->date)->format('M') === $month;
                            }
                            return false; // Skip jika bukan model
                        });
                        $totalValue = 0;
                        $count = 0;
                        foreach ($monthTargets as $target) {
                            if ($target instanceof App\Models\UserTarget) { // Pastikan namespace sesuai
                                if ($program->type === 'boolean') {
                                    $totalValue += $target->value == 1 ? 100 : 0;
                                } else { // numeric
                                    $totalValue += ($target->value / $program->target) * 100;
                                }
                                $count++;
                            }
                        }
                        $data[] = $count > 0 ? round($totalValue / $count, 2) : 0;
                    }
                }
                $series[] = [
                    'name' => $program->nama_program,
                    'data' => $data,
                    'dashArray' => $programs->keys()->search($program->id) === 2 ? 4 : 0 // Garis putus-putus untuk program ketiga
                ];
            }

            $chartData = [
                'categories' => $categories,
                'series' => $series,
            ];

            // Debugging: Log data chart untuk memastikan format tanggal dan data benar
            \Log::info('Chart Data for period ' . $period, [
                'categories' => $categories,
                'series' => $series,
                'programTargets' => $programTargets->toArray(),
            ]);

            return response()->json(['chartData' => $chartData]);
        } catch (\Exception $e) {
            \Log::error('Error in updateDashboardChart: ' . $e->getMessage(), [
                'period' => $request->input('period'),
                'user_id' => $user->id ?? null,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Terjadi kesalahan server. Silakan coba lagi.'], 500);
        }
    }



    private function calculateOverallStats($user, $period)
    {
        $startDate = $period === 'weekly' ? now()->subDays(7)->startOfDay() : now()->subDays(30)->startOfDay();

        $targets = UserTarget::where('user_id', $user->id)
            ->where('date', '>=', $startDate)
            ->with('program')
            ->get();

        $totalPrograms = Program::whereIn('level', [$user->level, 'both'])->count();
        $completedTargets = 0;
        $totalProgress = 0;

        foreach ($targets as $target) {
            $program = $target->program;
            if ($program->type === 'boolean') {
                if ($target->value == 1) {
                    $completedTargets++;
                    $totalProgress += 100;
                }
            } else { // numeric
                $progress = ($target->value / $program->target) * 100;
                $totalProgress += $progress;
                if ($progress >= 100) {
                    $completedTargets++;
                }
            }
        }

        $averageProgress = $totalPrograms > 0 ? round($totalProgress / $totalPrograms, 2) : 0;
        $completedPercentage = $totalPrograms > 0 ? round(($completedTargets / $totalPrograms) * 100, 2) : 0;

        return [
            'averageProgress' => $averageProgress, // Rata-rata presentase keberhasilan
            'completedPercentage' => $completedPercentage, // Persentase target selesai
            'completedTargets' => $completedTargets, // Jumlah target selesai
            'totalPrograms' => $totalPrograms, // Total target
        ];
    }

    private function prepareChartDataByMonth($targets, $program, $months)
    {
        $dates = [];
        $percentages = [];

        $startDate = now()->subMonths($months - 1)->startOfMonth(); // Mulai dari 6 bulan terakhir
        $groupedByMonth = $targets->groupBy(function ($target) {
            return \Carbon\Carbon::parse($target->date)->format('M'); // Kelompokkan berdasarkan nama bulan singkat (Jan, Feb, dll.)
        });

        // Ambil bulan unik dalam 6 bulan terakhir, urutkan dari terlama ke terbaru
        $monthsList = [];
        $currentDate = now()->subMonths($months - 1);
        for ($i = 0; $i < $months; $i++) {
            $month = $currentDate->format('M'); // Misalnya "Sep", "Oct", dll.
            if (!in_array($month, $monthsList)) {
                $monthsList[] = $month;
            }
            $currentDate->addMonth();
        }

        foreach ($monthsList as $month) {
            $dates[] = $month; // Sumbu X menampilkan nama bulan singkat
            $monthTargets = $groupedByMonth->get($month, collect());
            $totalValue = 0;
            $count = 0;

            foreach ($monthTargets as $target) {
                if ($program->type === 'boolean') {
                    $totalValue += $target->value == 1 ? 100 : 0;
                } else { // numeric
                    $totalValue += ($target->value / $program->target) * 100;
                }
                $count++;
            }

            $percentages[] = $count > 0 ? round($totalValue / $count, 2) : 0; // Rata-rata presentase untuk bulan tersebut
        }

        return [
            'categories' => $dates,
            'series' => [$percentages],
        ];
    }
}
