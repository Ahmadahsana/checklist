@extends('layouts.vertical', ['title' => 'Detail Presensi Kegiatan'])

@section('content')
    @include("layouts.shared.page-title", ["subtitle" => "Monitoring", "title" => "Detail Presensi Kegiatan"])

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">{{ $kegiatan->nama_kegiatan }}</h1>
        <p class="text-sm text-gray-600 mb-2">Tanggal: {{ $kegiatan->tanggal }}</p>
        <p class="text-sm text-gray-600 mb-2">Tipe: {{ ucfirst($kegiatan->tipe) }}</p>
        <p class="text-sm text-gray-600 mb-4">Kode Presensi: {{ $kegiatan->kode_presensi }}</p>

        <div class="mb-6">
            <h3 class="text-lg font-semibold">Statistik Kehadiran</h3>
            <p>Total Peserta: {{ $totalPeserta }}</p>
            <p>Hadir: {{ $hadir }} ({{ $kegiatan->persentase_kehadiran }}%)</p>
            <p>Tidak Hadir: {{ $tidakHadir }} ({{ $kegiatan->persentase_ketidakhadiran }}%)</p>
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
                    @foreach ($presensi as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->user }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ ucfirst($item->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection