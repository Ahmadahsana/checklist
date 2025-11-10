@extends('layouts.vertical', ['title' => 'Detail Presensi Kegiatan'])

@section('content')
    @include("layouts.shared.page-title", ["subtitle" => "Monitoring", "title" => "Detail Presensi Kegiatan"])

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">{{ $kegiatan->nama_kegiatan }}</h1>
        <p class="text-sm text-gray-600 mb-2">Tanggal: {{ $kegiatan->tanggal }}</p>
        <p class="text-sm text-gray-600 mb-2">Tipe: {{ ucfirst($kegiatan->jenis) }}</p>
        <p class="text-sm text-gray-600 mb-4">Kode Presensi: {{ $kegiatan->kode_unik }}</p>

        <div class="mb-6">
            <h3 class="text-lg font-semibold">Statistik Kehadiran</h3>
            <p>Total Peserta: {{ $totalPeserta }}</p>
            <p>Hadir: {{ $hadir }} ({{ $persentaseKehadiran }}%)</p>
            <p>Tidak Hadir: {{ $tidakHadir }} ({{ $persentaseKetidakhadiran }}%)</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Peserta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($presensis as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->user->nama_lengkap ?? $item->user->username ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $item->hadir ? 'Hadir' : 'Tidak hadir' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data presensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($belumHadirUsers->isNotEmpty() || $presensiTidakHadir->isNotEmpty())
            <div class="mt-8">
                <h3 class="text-lg font-semibold mb-4">Belum / Tidak Hadir</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @php $row = 1; @endphp
                            @foreach ($presensiTidakHadir as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $row++ }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->user->nama_lengkap ?? $item->user->username ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">Tidak hadir</td>
                                </tr>
                            @endforeach
                            @foreach ($belumHadirUsers as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $row++ }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->nama_lengkap ?? $user->username ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600">Belum hadir</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
