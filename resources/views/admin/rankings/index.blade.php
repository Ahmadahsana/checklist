@extends('layouts.vertical', ['title' => 'Ranking Bulanan'])

@section('content')
    @include('layouts.shared.page-title', ['subtitle' => 'Admin', 'title' => 'Ranking User Bulanan'])

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('admin.rank') }}" class="flex flex-col gap-4 md:flex-row md:items-center">
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700">Pilih Bulan</label>
                    <p class="text-xs text-gray-500">Ranking dihitung dari data `progress_bulanans`.</p>
                </div>
                <div class="flex items-center gap-3">
                    <select name="bulan" id="bulan"
                        class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"
                        onchange="this.form.submit()">
                        @foreach ($availableBulan as $bulan)
                            <option value="{{ $bulan }}" {{ $bulan == $selectedBulan ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($bulan . '-01')->isoFormat('MMMM YYYY') }}
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('admin.rank') }}"
                        class="text-sm text-blue-600 hover:underline">Reset</a>
                </div>
            </form>
        </div>

        @if ($rankings->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-6 text-center text-sm text-gray-500">
                Belum ada data progres untuk
                <strong>{{ \Carbon\Carbon::parse($selectedBulan . '-01')->isoFormat('MMMM YYYY') }}</strong>.
            </div>
        @else
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Ranking Bulanan</h2>
                        <p class="text-sm text-gray-500">Urutan berdasarkan nilai rata-rata pencapaian per user.</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-4 py-1 text-xs font-semibold uppercase tracking-wider text-blue-600">
                        {{ \Carbon\Carbon::parse($selectedBulan . '-01')->isoFormat('MMMM YYYY') }}
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
                    @foreach ($rankings->take(3) as $ranking)
                        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-blue-800">
                            <p class="text-xs font-medium uppercase tracking-wide">Top {{ $ranking->rank }}</p>
                            <h3 class="mt-1 text-lg font-semibold">{{ $ranking->nama_lengkap }}</h3>
                            <p class="text-sm">Rata-rata pencapaian:
                                <span class="font-semibold">{{ number_format($ranking->value, 2) }}%</span>
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Rank</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Nilai rata-rata</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @foreach ($rankings as $ranking)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-gray-900">#{{ $ranking->rank }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $ranking->nama_lengkap }}</p>
                                        <p class="text-xs text-gray-500">User ID: {{ $ranking->user_id }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-24 rounded-full bg-gray-100">
                                                <span class="block h-full rounded-full bg-emerald-500"
                                                    style="width: {{ min(100, $ranking->value) }}%"></span>
                                            </div>
                                            <span class="font-medium text-gray-900">{{ number_format($ranking->value, 2) }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('users.show', $ranking->user_id) }}"
                                            class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
