<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Program;
use App\Models\UserTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProgressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Tambahkan middleware role jika ada
        // $this->middleware('role:admin');
    }

    public function index()
    {
        $users = User::where('level', '!=', 'admin')->get(); // Ambil semua user kecuali admin
        $programs = Program::all(); // Ambil semua program untuk chart keseluruhan

        return view('admin.progress.index', compact('users', 'programs'));
    }

    public function update(Request $request)
    {
        $userId = $request->input('user_id');
        $period = $request->input('period', 'weekly'); // Default ke "weekly"

        if (!$userId) {
            return response()->json(['error' => 'User tidak dipilih.'], 400);
        }

        $user = User::findOrFail($userId);
        $startDate = $period === 'weekly' ? now()->subDays(7)->startOfDay() : now()->subMonths(6)->startOfMonth();

        $programs = Program::whereIn('level', [$user->level, 'both'])->get();
        $categories = [];
        $series = [];

        if ($period === 'weekly') {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $categories[] = $date; // Gunakan format numerik (YYYY-MM-DD) untuk mingguan
            }
        } else { // monthly (6 bulan terakhir)
            $categories = $this->prepareChartDataByMonth($user, $programs, 6);
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
                        if ($target instanceof App\Models\UserTarget) {
                            return \Carbon\Carbon::parse($target->date)->format('M') === $month;
                        }
                        return false; // Skip jika bukan model
                    });
                    $totalValue = 0;
                    $count = 0;
                    foreach ($monthTargets as $target) {
                        if ($target instanceof App\Models\UserTarget) {
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
                'dashArray' => 0 // Tidak perlu garis putus-putus untuk sekarang
            ];
        }

        $chartData = [
            'categories' => $categories,
            'series' => $series,
        ];

        // \Log::info('Admin Progress Chart Data for user ' . $userId . ' and period ' . $period, $chartData);

        return response()->json(['chartData' => $chartData]);
    }

    private function prepareChartDataByMonth($user, $programs, $months)
    {
        $categories = [];
        $startDate = now()->subMonths($months - 1)->startOfMonth(); // Mulai dari 6 bulan terakhir

        $programTargets = UserTarget::where('user_id', $user->id)
            ->where('date', '>=', $startDate)
            ->with('program')
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(function ($target) {
                return \Carbon\Carbon::parse($target->date)->format('M'); // Kelompokkan berdasarkan nama bulan singkat (Sep, Oct, dll.)
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

        $categories = array_reverse($monthsList); // Urutkan dari terlama ke terbaru (Sep, Oct, ..., Feb)

        return $categories;
    }

    public function overallProgress()
    {
        $users = User::where('level', '!=', 'admin')->get();
        $programs = Program::all();

        $overallProgress = [];
        foreach ($programs as $program) {
            $totalValue = 0;
            $count = 0;

            foreach ($users as $user) {
                $userTargets = UserTarget::where('user_id', $user->id)
                    ->where('program_id', $program->id)
                    ->where('date', '>=', now()->subMonths(12)->startOfMonth())
                    ->get();

                foreach ($userTargets as $target) {
                    if ($program->type === 'boolean') {
                        $totalValue += $target->value == 1 ? 100 : 0;
                    } else { // numeric
                        $totalValue += ($target->value / $program->target) * 100;
                    }
                    $count++;
                }
            }

            $average = $count > 0 ? round($totalValue / $count, 2) : 0;
            $overallProgress[$program->nama_program] = $average;
        }

        // Debugging: Log data untuk memastikan format benar
        // \Log::info('Overall Progress Data', ['overallProgress' => $overallProgress, 'programs' => $programs->pluck('nama_program')->toArray()]);

        return view('admin.progress.overall', compact('overallProgress', 'programs'));
    }

    public function showUserProgress()
    {
        $users = User::where('level', '!=', 'admin')->get();
        $programs = Program::all(); // Ambil semua program untuk chart

        return view('admin.progress.user', compact('users', 'programs'));
    }

    public function searchUsers(Request $request)
    {
        $users = User::where('level', '!=', 'admin')
            ->when($request->input('search'), function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('id', $search);
            })
            ->get();

        return response()->json(['users' => $users]);
    }

    public function userProgressUpdate(Request $request)
    {
        $userId = $request->input('user_id');
        $period = $request->input('period', 'weekly'); // Default ke "weekly"

        if (!$userId) {
            return response()->json(['error' => 'User tidak dipilih.'], 400);
        }

        $user = User::findOrFail($userId);
        $startDate = $period === 'weekly' ? now()->subDays(7)->startOfDay() : now()->subMonths(6)->startOfMonth();

        $programs = Program::whereIn('level', [$user->level, 'both'])->get();
        $categories = [];
        $series = [];

        if ($period === 'weekly') {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $categories[] = $date; // Gunakan format numerik (YYYY-MM-DD) untuk mingguan
            }
        } else { // monthly (6 bulan terakhir)
            $categories = $this->prepareChartDataByMonth($user, $programs, 6);
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
                        if ($target instanceof App\Models\UserTarget) {
                            return \Carbon\Carbon::parse($target->date)->format('M') === $month;
                        }
                        return false; // Skip jika bukan model
                    });
                    $totalValue = 0;
                    $count = 0;
                    foreach ($monthTargets as $target) {
                        if ($target instanceof App\Models\UserTarget) {
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
                'dashArray' => 0 // Tidak perlu garis putus-putus untuk sekarang
            ];
        }

        $chartData = [
            'categories' => $categories,
            'series' => $series,
        ];

        // \Log::info('Admin User Progress Chart Data for user ' . $userId . ' and period ' . $period, $chartData);

        return response()->json(['chartData' => $chartData]);
    }
}
