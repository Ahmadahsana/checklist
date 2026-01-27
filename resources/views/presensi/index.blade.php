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
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($item->jenis == 'rutin')
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs" title="Berubah tiap minggu">{{ $item->kode_mingguan }}</span>
                                @else
                                    {{ $item->kode_mingguan }}
                                @endif
                            </td>
                            {{-- <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $item->persentase_kehadiran }}% (Hadir) / {{ $item->persentase_ketidakhadiran }}% (Tidak Hadir)
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                <a href="{{ route('presensi.show', $item) }}" class="text-blue-600 hover:underline">Detail</a>
                                <form action="{{ route('presensi.destroy', $item) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline"
                                        onclick="return confirm('Hapus kegiatan ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
