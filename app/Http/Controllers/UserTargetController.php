<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgressBulanan;
use App\Models\UserTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    // public function store(Request $request)
    // {
    //     // if (!Gate::allows('is-user')) {
    //     //     abort(403, 'Akses ditolak. Hanya user biasa yang dapat mengakses halaman ini.');
    //     // }

    //     $user = Auth::user();
    //     $date = $request->input('date');

    //     // Debugging: Log data yang diterima dari form
    //     // \Log::info('Received target data for date ' . $date, $request->all());

    //     $request->validate([
    //         'date' => 'required|date|before_or_equal:' . now()->toDateString() . '|after_or_equal:' . now()->subDays(7)->toDateString(),
    //         'targets' => 'required|array',
    //         'targets.*.program_id' => 'required|exists:programs,id',
    //         'targets.*.value' => [
    //             'required',
    //             function ($attribute, $value, $fail) {
    //                 $programId = explode('.', $attribute)[1]; // Ambil program_id dari atribut
    //                 $program = Program::find($programId);
    //                 if (!$program) {
    //                     $fail('Program tidak ditemukan.');
    //                     return;
    //                 }
    //                 if ($program->type === 'numeric' && (!is_numeric($value) || $value < 0)) {
    //                     $fail('Nilai harus angka positif untuk target numeric.');
    //                 }
    //                 if ($program->type === 'boolean' && !in_array($value, [0, 1])) {
    //                     $fail('Nilai harus 0 (Tidak) atau 1 (Ya) untuk target boolean.');
    //                 }
    //             },
    //         ],
    //     ]);

    //     // Simpan semua target untuk tanggal yang dipilih
    //     foreach ($request->input('targets') as $programData) {
    //         $programId = $programData['program_id'];
    //         $value = $programData['value'];
    //         $program = Program::find($programId);

    //         $existingTarget = UserTarget::where('user_id', $user->id)
    //             ->where('program_id', $programId)
    //             ->where('date', $date)
    //             ->first();

    //         $valueToSave = $program->type === 'boolean' ? ($value === '1' ? 1 : 0) : $value;

    //         if ($existingTarget) {
    //             $existingTarget->update(['value' => $valueToSave, 'status' => 'completed']);
    //         } else {
    //             UserTarget::create([
    //                 'user_id' => $user->id,
    //                 'program_id' => $programId,
    //                 'date' => $date,
    //                 'value' => $valueToSave,
    //                 'status' => 'completed',
    //             ]);
    //         }
    //     }

    //     // Debugging: Log data yang disimpan
    //     // \Log::info('Targets saved for date ' . $date, $request->input('targets'));

    //     return redirect()->route('user-targets.index', ['date' => $date])->with('success', 'Semua target berhasil disimpan');
    // }

    public function store(Request $request)
    {
        $user = Auth::user();
        $date = $request->input('date');

        // Validasi input
        $request->validate([
            'date' => 'required|date|before_or_equal:' . now()->toDateString() . '|after_or_equal:' . now()->subDays(7)->toDateString(),
            'targets' => 'required|array',
            'targets.*.program_id' => 'required|exists:programs,id',
            'targets.*.value' => [
                'required',
                function ($attribute, $value, $fail) {
                    $programId = explode('.', $attribute)[1];
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

        // Mulai transaksi untuk konsistensi data
        DB::beginTransaction();

        try {
            $updatedProgramIds = [];

            // Simpan atau update semua target
            foreach ($request->input('targets') as $programData) {
                $programId = $programData['program_id'];
                $value = $programData['value'];
                $program = Program::find($programId);

                $valueToSave = $program->type === 'boolean' ? ($value === '1' ? 1 : 0) : $value;
                $score = $this->calculatePercentage($program, $valueToSave);

                $existingTarget = UserTarget::where('user_id', $user->id)
                    ->where('program_id', $programId)
                    ->where('date', $date)
                    ->first();

                if ($existingTarget) {
                    // Jika update, simpan nilai lama untuk perhitungan inkremental
                    $oldValue = $existingTarget->value;
                    $existingTarget->update([
                        'value' => $valueToSave,
                        'status' => 'completed',
                        'score' => $score,
                    ]);
                } else {
                    // Jika insert, tidak ada nilai lama
                    $oldValue = null;
                    UserTarget::create([
                        'user_id' => $user->id,
                        'program_id' => $programId,
                        'date' => $date,
                        'value' => $valueToSave,
                        'status' => 'completed',
                        'score' => $score,
                    ]);
                }

                // Simpan program_id untuk pembaruan progres bulanan
                $updatedProgramIds[] = [
                    'program_id' => $programId,
                    'value' => $valueToSave,
                    'old_value' => $oldValue,
                ];
            }

            // Update progres bulanan
            $this->updateMonthlyProgress($user->id, $date, $updatedProgramIds);

            DB::commit();

            return redirect()->route('user-targets.index', ['date' => $date])
                ->with('success', 'Semua target berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error jika diperlukan
            // \Log::error('Error saving targets: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan target.');
        }
    }

    protected function updateMonthlyProgress($userId, $date, $updatedProgramIds)
    {
        // Tentukan bulan dari date
        $bulan = date('Y-m', strtotime($date));

        // Ambil semua program yang diupdate
        $programIds = array_column($updatedProgramIds, 'program_id');
        $programs = Program::whereIn('id', $programIds)->get()->keyBy('id');

        // Mulai transaksi untuk progres bulanan
        DB::beginTransaction();

        try {
            foreach ($updatedProgramIds as $updated) {
                $programId = $updated['program_id'];
                $value = $updated['value'];
                $oldValue = $updated['old_value'];
                $program = $programs[$programId];

                // Hitung persentase untuk record baru/updated
                $persentase = $this->calculatePercentage($program, $value);

                // Jika ada nilai lama (update), hitung persentase lama
                $oldPersentase = $oldValue !== null ? $this->calculatePercentage($program, $oldValue) : null;

                // Ambil atau buat record progress_bulanan
                $progress = ProgressBulanan::firstOrCreate(
                    ['user_id' => $userId, 'bulan' => $bulan],
                    ['total_persentase' => 0, 'jumlah_record' => 0, 'value' => 0]
                );

                // Update inkremental
                if ($oldPersentase !== null) {
                    // Update: Kurangi persentase lama, tambah persentase baru
                    $progress->total_persentase = $progress->total_persentase - $oldPersentase + $persentase;
                } else {
                    // Insert: Tambah persentase baru, tambah jumlah record
                    $progress->total_persentase += $persentase;
                    $progress->jumlah_record += 1;
                }

                // Hitung rata-rata
                $progress->value = $progress->jumlah_record > 0
                    ? $progress->total_persentase / $progress->jumlah_record
                    : 0;

                $progress->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error jika diperlukan
            // \Log::error('Error updating monthly progress: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function calculatePercentage($program, $value)
    {
        if ($program->type === 'boolean') {
            return $value == 1 ? 100 : 0;
        }

        if ($program->type === 'numeric') {
            return min(($value / $program->target) * 100, 100);
        }

        return 0; // Default jika tipe tidak dikenal
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

        $scores = $targets->map(function ($target) {
            return $this->calculateTargetScore($target);
        })->filter(fn($val) => is_numeric($val));

        $totalRecords = $scores->count();
        if ($totalRecords === 0) {
            return 0;
        }

        return round($scores->avg(), 2);
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
                $data[] = $target ? ($this->calculateTargetScore($target) ?? 0) : 0;
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

    private function calculateTargetScore(UserTarget $target): ?float
    {
        $program = $target->program;

        if (!$program) {
            return null;
        }

        if ($target->score !== null) {
            return (float) $target->score;
        }

        if ($program->type === 'boolean') {
            return $target->value ? 100 : 0;
        }

        $programTarget = (float) ($program->target ?? 0);
        if ($programTarget <= 0) {
            return null;
        }

        return max(0, min(100, round(($target->value / $programTarget) * 100, 2)));
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
                $startMonth = Carbon::now()->subMonths(5)->startOfMonth();
                for ($i = 0; $i < 6; $i++) {
                    $categories[] = $startMonth->copy()->addMonths($i)->format('M');
                }
            }

            foreach ($programs as $program) {
                $programTargets = UserTarget::where('user_id', $user->id)
                    ->where('program_id', $program->id)
                    ->where('date', '>=', $startDate)
                    ->orderBy('date', 'asc')
                    ->get()
                    ->groupBy('date');

                $flatTargets = $programTargets->flatten();

                $data = [];
                foreach ($categories as $category) {
                    if ($period === 'weekly') {
                        $date = $category; // Format Y-m-d langsung digunakan
                        $target = $programTargets->get($date, collect())->first();
                        if (!$target) {
                            $data[] = 0;
                            continue;
                        }

                        if ($program->type === 'boolean') {
                            $data[] = $target->value ? 100 : 0;
                        } else {
                            $programTarget = (float) ($program->target ?? 0);
                            $data[] = $programTarget > 0 ? ($target->value / $programTarget) * 100 : 0;
                        }
                    } else { // monthly
                        $month = $category; // Nama bulan singkat (Sep, Oct, dll.)
                        $monthTargets = $flatTargets->filter(function ($target) use ($month) {
                            return $target instanceof UserTarget
                                && Carbon::parse($target->date)->format('M') === $month;
                        });

                        $count = $monthTargets->count();
                        if ($count === 0) {
                            $data[] = 0;
                            continue;
                        }

                        $totalValue = $monthTargets->reduce(function ($carry, $target) use ($program) {
                            if ($program->type === 'boolean') {
                                return $carry + ($target->value == 1 ? 100 : 0);
                            }
                            $programTarget = (float) ($program->target ?? 0);
                            if ($programTarget <= 0) {
                                return $carry;
                            }
                            return $carry + (($target->value / $programTarget) * 100);
                        }, 0);

                        $data[] = round($totalValue / $count, 2);
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
            // \Log::info('Chart Data for period ' . $period, [
            //     'categories' => $categories,
            //     'series' => $series,
            //     'programTargets' => $programTargets->toArray(),
            // ]);

            return response()->json(['chartData' => $chartData]);
        } catch (\Exception $e) {
            // \Log::error('Error in updateDashboardChart: ' . $e->getMessage(), [
            //     'period' => $request->input('period'),
            //     'user_id' => $user->id ?? null,
            //     'stack_trace' => $e->getTraceAsString(),
            // ]);
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
