@extends('layouts.vertical', ['title' => 'Admin Dashboard'])

@section('content')
    @include('layouts.shared.page-title', ['subtitle' => 'Admin', 'title' => 'Dashboard'])

    <div class="space-y-6">
        <div class="flex flex-col gap-2 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Ringkasan Harian</p>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $todayDate->isoFormat('dddd, D MMMM YYYY') }}
                </h1>
                <p class="text-sm text-gray-500">Pantau capaian user pada hari ini untuk memastikan setiap program berjalan.</p>
            </div>
            <div class="rounded-xl bg-blue-50 px-6 py-3 text-right">
                <p class="text-xs uppercase tracking-wide text-blue-600">Completion Rate</p>
                <p class="text-3xl font-semibold text-blue-900">{{ $completionRateToday }}%</p>
                <p class="text-xs text-blue-500">Pengguna yang menyelesaikan catatan hari ini</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total Peserta</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalUsers }}</p>
                <p class="text-xs text-gray-400">User role `user` terdaftar</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">User Update Hari Ini</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $usersCompletedToday }}</p>
                <p class="text-xs text-gray-400">Mengirim catatan completed</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Catatan Selesai</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $completedRecordsToday }}</p>
                <p class="text-xs text-gray-400">Target berstatus completed</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Catatan Pending</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $pendingRecordsToday }}</p>
                <p class="text-xs text-gray-400">Target yang belum selesai</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Top Performer Hari Ini</h2>
                        <p class="text-sm text-gray-500">5 pengguna dengan pencapaian rata-rata tertinggi.</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-600">
                        {{ $topUsersToday->count() }} User
                    </span>
                </div>
                <ul class="mt-5 space-y-3">
                    @forelse ($topUsersToday as $item)
                        <li class="flex items-center justify-between rounded-xl border border-gray-100 px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ $item['user']->nama_lengkap ?? $item['user']->username }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $item['completed'] }} selesai / {{ $item['total'] }} tugas
                                    • Update terakhir {{ $item['lastUpdate'] ? \Carbon\Carbon::parse($item['lastUpdate'])->diffForHumans() : '–' }}
                                </p>
                            </div>
                            <span class="text-lg font-semibold text-emerald-600">{{ $item['averageAchievement'] }}%</span>
                        </li>
                    @empty
                        <li class="rounded-xl border border-dashed border-gray-200 px-4 py-6 text-center text-sm text-gray-500">
                            Belum ada aktivitas hari ini.
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-gray-900">Aktivitas Terbaru</h2>
                <p class="text-sm text-gray-500">Catatan yang baru diperbarui.</p>
                <ul class="mt-4 space-y-3">
                    @forelse ($recentActivities as $activity)
                        <li class="rounded-lg border border-gray-100 px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $activity['user']->nama_lengkap ?? $activity['user']->username }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $activity['program']->nama_program ?? '-' }} •
                                {{ $activity['status'] === 'completed' ? 'Completed' : 'Pending' }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $activity['updated_at'] ? \Carbon\Carbon::parse($activity['updated_at'])->diffForHumans() : '–' }}
                            </p>
                        </li>
                    @empty
                        <li class="rounded-lg border border-dashed border-gray-200 px-4 py-6 text-center text-sm text-gray-500">
                            Belum ada aktivitas tercatat.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Capaian Program Hari Ini</h2>
                    <p class="text-sm text-gray-500">Rata-rata pencapaian per program untuk aktivitas hari ini.</p>
                </div>
                <span class="rounded-full bg-blue-50 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-blue-600">
                    {{ $programSummariesToday->count() }} Program
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium uppercase tracking-wide text-gray-500">Program</th>
                            <th class="px-4 py-3 text-left font-medium uppercase tracking-wide text-gray-500">% Rata-rata</th>
                            <th class="px-4 py-3 text-left font-medium uppercase tracking-wide text-gray-500">Peserta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($programSummariesToday as $summary)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $summary['program']->nama_program }}</p>
                                    <p class="text-xs text-gray-500">Level: {{ $summary['program']->level ?? '-' }} | {{ ucfirst($summary['program']->type ?? 'numeric') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-24 rounded-full bg-gray-100">
                                            <span class="block h-full rounded-full bg-blue-500"
                                                style="width: {{ min(100, $summary['averageAchievement']) }}%"></span>
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $summary['averageAchievement'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $summary['participants'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">
                                    Belum ada capaian program hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
