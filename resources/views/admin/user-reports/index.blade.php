@extends('layouts.vertical', ['title' => 'Laporan User'])

@section('content')
    @include('layouts.shared.page-title', ['subtitle' => 'Admin', 'title' => 'Laporan User'])

    <div class="bg-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan User</h1>
                <p class="text-sm text-gray-500">Pilih user untuk melihat laporan bulanan per minggu.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Level</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($users as $index => $user)
                        <tr class="odd:bg-white even:bg-gray-50">
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $user->nama_lengkap ?? $user->username }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ ucfirst($user->level) }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.user-reports.show', ['user' => $user->id, 'month' => $currentMonth]) }}"
                                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-white text-xs font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Lihat laporan
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
