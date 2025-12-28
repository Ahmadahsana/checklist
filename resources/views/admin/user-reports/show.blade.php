@extends('layouts.vertical', ['title' => 'Laporan User'])

@section('content')
    @include('layouts.shared.page-title', ['subtitle' => 'Admin', 'title' => 'Laporan User'])

    <div class="bg-white p-6 rounded-xl shadow-lg space-y-6">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan {{ $user->nama_lengkap ?? $user->username }}</h1>
                <p class="text-sm text-gray-500">Rincian capaian per minggu dalam satu bulan.</p>
            </div>
            <a href="{{ route('admin.user-reports.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke daftar</a>
        </div>

        <form method="GET" action="{{ route('admin.user-reports.show', $user->id) }}" class="flex flex-col gap-3 md:flex-row md:items-end">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700">Pilih Bulan</label>
                <input type="month" id="month" name="month" value="{{ $month }}"
                    class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Tampilkan
                </button>
                <a href="{{ route('admin.user-reports.show', ['user' => $user->id, 'month' => now()->format('Y-m')]) }}"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Reset ke bulan ini
                </a>
            </div>
        <div class="flex justify-end">
            <a href="{{ route('admin.user-reports.export', ['user' => $user->id, 'month' => $month]) }}"
               class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                Export Excel (XLSX)
            </a>
        </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border border-gray-200 px-4 py-2 text-left font-semibold uppercase text-gray-600">No</th>
                        <th class="border border-gray-200 px-4 py-2 text-left font-semibold uppercase text-gray-600">Program</th>
                        <th class="border border-gray-200 px-4 py-2 text-center font-semibold uppercase text-gray-600">Target Mingguan</th>
                        <th class="border border-gray-200 px-4 py-2 text-center font-semibold uppercase text-gray-600">Minggu 1</th>
                        <th class="border border-gray-200 px-4 py-2 text-center font-semibold uppercase text-gray-600">Minggu 2</th>
                        <th class="border border-gray-200 px-4 py-2 text-center font-semibold uppercase text-gray-600">Minggu 3</th>
                        <th class="border border-gray-200 px-4 py-2 text-center font-semibold uppercase text-gray-600">Minggu 4</th>
                        <th class="border border-gray-200 px-4 py-2 text-center font-semibold uppercase text-gray-600">Total</th>
                        <th class="border border-gray-200 px-4 py-2 text-center font-semibold uppercase text-gray-600">Presentase</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report as $idx => $row)
                        <tr class="odd:bg-white even:bg-gray-50">
                            <td class="border border-gray-200 px-4 py-2 text-left">{{ $idx + 1 }}</td>
                            <td class="border border-gray-200 px-4 py-2 text-left">{{ $row['program']->nama_program }}</td>
                            <td class="border border-gray-200 px-4 py-2 text-center">{{ $row['weeklyTarget'] }}</td>
                            @foreach ($row['weeks'] as $value)
                                <td class="border border-gray-200 px-4 py-2 text-center">{{ $value }}</td>
                            @endforeach
                            <td class="border border-gray-200 px-4 py-2 text-center">{{ $row['totalScore'] }}</td>
                            <td class="border border-gray-200 px-4 py-2 text-center font-semibold text-blue-700">{{ $row['percentage'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border border-gray-200 px-4 py-4 text-center text-gray-500">
                                Belum ada data pada bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- <div class="flex justify-end">
            <a href="{{ route('admin.user-reports.export', ['user' => $user->id, 'month' => $month]) }}"
               class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                Export Excel
            </a>
        </div> --}}

        <div class="text-xs text-gray-500">
            Kolom minggu dan total menampilkan capaian angka (bukan %). Presentase dihitung dari total capaian dibanding target mingguan x 4.
        </div>
    </div>
@endsection
