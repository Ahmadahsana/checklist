@extends('layouts.vertical', ['title' => 'Presensi Kegiatan'])

@section('content')
    @include("layouts.shared.page-title", ["subtitle" => "Monitoring", "title" => "Presensi Kegiatan"])

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Daftar Kegiatan</h1>
            <a href="{{ route('presensi.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah Kegiatan</a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kegiatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam mulai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Presensi</th>
                        {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kehadiran</th> --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($kegiatan as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->nama_kegiatan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->tanggal }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->jam_mulai }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ ucfirst($item->jenis) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->kode_unik }}</td>
                            {{-- <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $item->persentase_kehadiran }}% (Hadir) / {{ $item->persentase_ketidakhadiran }}% (Tidak Hadir)
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('presensi.show', $item) }}" class="text-blue-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection