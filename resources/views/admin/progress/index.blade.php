@extends('layouts.vertical', ['title' => 'Ringkasan Progress'])

@section('content')
    @include('layouts.shared.page-title', ['subtitle' => 'Admin', 'title' => 'Ringkasan Progress User'])

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total Peserta Aktif</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalUsers }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Rata-rata Pencapaian</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $avgAchievement }}%</p>
                <div class="mt-2 h-2 rounded-full bg-gray-100">
                    <span class="block h-full rounded-full bg-blue-600" style="width: {{ min(100, $avgAchievement) }}%"></span>
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Program Terdaftar</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalPrograms }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Catatan Selesai Hari Ini</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $completedToday }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Top Performer</h2>
                <p class="text-sm text-gray-500">3 pengguna dengan pencapaian rata-rata tertinggi</p>
                <ul class="mt-4 space-y-3">
                    @forelse ($topPerformers as $index => $summary)
                        <li class="flex items-center justify-between rounded-lg border border-gray-100 p-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $summary['user']->nama_lengkap ?? $summary['user']->username }}
                                </p>
                                <p class="text-xs text-gray-500">Level: {{ $summary['user']->level ?? '-' }}</p>
                            </div>
                            <span class="text-sm font-semibold text-blue-600">{{ $summary['averageAchievement'] }}%</span>
                        </li>
                    @empty
                        <li class="rounded-lg border border-dashed border-gray-200 p-3 text-center text-sm text-gray-500">
                            Belum ada data progress.
                        </li>
                    @endforelse
                </ul>
            </div>
            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Catatan</h2>
                <p class="text-sm text-gray-500">
                    Gunakan tabel di bawah untuk memantau ketercapaian setiap user dan tindak lanjuti pengguna dengan pencapaian rendah.
                </p>
                <div class="mt-4 rounded-lg border border-dashed border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                    <p class="font-semibold text-gray-800">Tips Monitoring</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li>Perhatikan kolom “Terakhir Update” untuk melihat siapa yang tidak aktif.</li>
                        <li>Kolom “Catatan Selesai” membantu memantau kepatuhan pengisian.</li>
                        <li>Gunakan tombol “Detail” untuk melihat progres per program.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Progress Seluruh User</h2>
                    <p class="text-sm text-gray-500">Ringkasan realtime berdasarkan catatan target.</p>
                </div>
                <div class="relative w-full md:w-64">
                    <input type="text" id="userProgressSearch"
                        class="block w-full rounded-lg border border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Cari nama pengguna...">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="userProgressTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Program Aktif</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Catatan Selesai</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Rata-rata %</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Completion Rate</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Terakhir Update</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($userSummaries as $index => $summary)
                            <tr class="text-sm text-gray-700">
                                <td class="px-4 py-3">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">
                                        {{ $summary['user']->nama_lengkap ?? $summary['user']->username }}
                                    </p>
                                    <p class="text-xs text-gray-500">Level: {{ $summary['user']->level ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $summary['activePrograms'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-green-600">{{ $summary['completedRecords'] }}</span>
                                    <span class="text-gray-400">/</span>
                                    <span class="text-gray-500">{{ $summary['totalRecords'] }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-20 rounded-full bg-gray-100">
                                            <span class="block h-full rounded-full bg-emerald-500"
                                                style="width: {{ min(100, $summary['averageAchievement']) }}%"></span>
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $summary['averageAchievement'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $summary['completionRate'] }}%</td>
                                <td class="px-4 py-3 text-xs">
                                    {{ $summary['recentUpdate'] ? \Carbon\Carbon::parse($summary['recentUpdate'])->isoFormat('D MMM YYYY') : 'Belum ada' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('users.show', $summary['user']) }}"
                                        class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-4 text-center text-sm text-gray-500">
                                    Belum ada catatan progress.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Capaian Per Program</h2>
                    <p class="text-sm text-gray-500">Monitoring rata-rata pencapaian dan partisipasi di setiap program.</p>
                </div>
                <span class="rounded-full bg-emerald-50 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-600">
                    {{ $programSummaries->count() }} Program
                </span>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($programSummaries as $summary)
                    <div class="rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $summary['program']->nama_program }}</p>
                                <p class="text-xs text-gray-500 capitalize">Level: {{ $summary['program']->level ?? '-' }} • {{ ucfirst($summary['program']->type ?? 'numeric') }}</p>
                            </div>
                            <span class="text-sm font-semibold text-emerald-600">{{ $summary['averageAchievement'] }}%</span>
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-gray-100">
                            <span class="block h-full rounded-full bg-emerald-500"
                                style="width: {{ min(100, $summary['averageAchievement']) }}%"></span>
                        </div>
                        <dl class="mt-3 grid grid-cols-2 gap-2 text-xs text-gray-600">
                            <div>
                                <dt class="text-gray-500">Catatan</dt>
                                <dd class="font-semibold text-gray-900">{{ $summary['totalRecords'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Peserta</dt>
                                <dd class="font-semibold text-gray-900">{{ $summary['participants'] }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-gray-500">Terakhir Update</dt>
                                <dd class="font-semibold text-gray-900">
                                    {{ $summary['recentUpdate'] ? \Carbon\Carbon::parse($summary['recentUpdate'])->isoFormat('D MMM YYYY') : 'Belum ada' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada data capaian program.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('userProgressSearch');
            const tableRows = document.querySelectorAll('#userProgressTable tbody tr');

            if (!searchInput) {
                return;
            }

            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase();

                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        });
    </script>
@endsection
