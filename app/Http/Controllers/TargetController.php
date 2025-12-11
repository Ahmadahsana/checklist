<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\User;
use App\Models\UserTarget;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function admin_dashboard()
    {
        $today = now()->toDateString();

        $totalUsers = User::where('role', 'user')->count();

        $todayTargets = UserTarget::with(['program', 'user'])
            ->whereDate('date', $today)
            ->get();

        $usersCompletedToday = $todayTargets->where('status', 'completed')->pluck('user_id')->unique()->count();
        $completionRateToday = $totalUsers > 0 ? round(($usersCompletedToday / $totalUsers) * 100, 1) : 0;

        $totalRecordsToday = $todayTargets->count();
        $completedRecordsToday = $todayTargets->where('status', 'completed')->count();
        $pendingRecordsToday = max(0, $totalRecordsToday - $completedRecordsToday);

        $topUsersToday = $todayTargets
            ->groupBy('user_id')
            ->map(function ($records) {
                $user = $records->first()->user;
                $scores = $records->map(function ($target) {
                    if (isset($target->score)) {
                        return $target->score;
                    }
                    return $this->calculateTargetAchievement($target);
                })->filter(function ($val) {
                    return is_numeric($val);
                });

                $averageAchievement = $scores->count() > 0 ? round($scores->avg(), 1) : 0;
                $completed = $records->where('status', 'completed')->count();
                $lastUpdate = optional($records->sortByDesc('updated_at')->first())->updated_at;

                return [
                    'user' => $user,
                    'averageAchievement' => $averageAchievement,
                    'completed' => $completed,
                    'total' => $records->count(),
                    'lastUpdate' => $lastUpdate,
                ];
            })
            ->sortByDesc('averageAchievement')
            ->values()
            ->take(5);

        $programSummariesToday = Program::with(['userTargets' => function ($query) use ($today) {
            $query->whereDate('date', $today);
        }])
            ->get()
            ->map(function ($program) {
                $eligibleUsers = User::where('role', 'user')
                    ->when($program->level && $program->level !== 'both', function ($q) use ($program) {
                        return $q->where('level', $program->level);
                    })
                    ->get();

                $totalEligible = $eligibleUsers->count();
                $targets = $program->userTargets;
                $targetByUser = $targets->keyBy('user_id');

                if ($totalEligible === 0) {
                    return [
                        'program' => $program,
                        'averageAchievement' => 0,
                        'records' => 0,
                        'participants' => 0,
                        'lastUpdate' => null,
                    ];
                }

                $scores = $eligibleUsers->map(function ($user) use ($targetByUser) {
                    $target = $targetByUser->get($user->id);

                    if (!$target) {
                        return 0;
                    }

                    if (isset($target->score)) {
                        return $target->score;
                    }

                    return $this->calculateTargetAchievement($target) ?? 0;
                });

                $averageAchievement = round($scores->avg() ?? 0, 1);

                return [
                    'program' => $program,
                    'averageAchievement' => $averageAchievement,
                    'records' => $targets->count(),
                    'participants' => $totalEligible,
                    'lastUpdate' => optional($targets->sortByDesc('updated_at')->first())->updated_at,
                ];
            })
            ->sortByDesc('averageAchievement')
            ->values();

        $recentActivities = $todayTargets
            ->sortByDesc('updated_at')
            ->take(5)
            ->map(function ($target) {
                return [
                    'user' => $target->user,
                    'program' => $target->program,
                    'value' => $target->value,
                    'status' => $target->status,
                    'updated_at' => $target->updated_at,
                ];
            });

        return view('admin.dashboard', [
            'todayDate' => now(),
            'totalUsers' => $totalUsers,
            'usersCompletedToday' => $usersCompletedToday,
            'completionRateToday' => $completionRateToday,
            'completedRecordsToday' => $completedRecordsToday,
            'pendingRecordsToday' => $pendingRecordsToday,
            'topUsersToday' => $topUsersToday,
            'programSummariesToday' => $programSummariesToday,
            'recentActivities' => $recentActivities,
        ]);
    }

    public function dashboard()
    {
        return view('starter-page');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function calculateTargetAchievement(UserTarget $target): ?float
    {
        $program = $target->program;

        if (!$program) {
            return null;
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
}
