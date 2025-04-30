@extends('layouts.vertical', ['title' => 'Riwayat Presensi'])

@section('content')
    @include("layouts.shared.page-title", ["subtitle" => "User", "title" => "Riwayat Presensi"])

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Riwayat Presensi</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($presensi->isEmpty())
            <p class="text-gray-600">Tidak ada riwayat presensi.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kegiatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Hadir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($presensi as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->kegiatan->nama_kegiatan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->kegiatan->tanggal }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->jam_hadir }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->kode_masuk }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->keterangan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection