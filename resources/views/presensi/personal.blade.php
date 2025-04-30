@extends('layouts.vertical', ['title' => 'Presensi Personal'])

@section('content')
    @include("layouts.shared.page-title", ["subtitle" => "User", "title" => "Presensi Personal"])

    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- <div></div> --}}
        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold">Presensi Personal</h1>
            <a href="/presensi-riwayat" class="bg-yellow-600 text-white px-2 py-1 rounded hover:bg-yellow-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                </svg>
                Riwayat Presensi
            </a>
        </div>
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


        <!-- Banner Informasi Presensi Aktif -->
        @if ($kegiatanAktif)
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded-lg mt-4" role="alert">
                <h3 class="font-semibold">Presensi Aktif Sekarang!</h3>
                <p>Kegiatan: {{ $kegiatanAktif->nama_kegiatan }}</p>
                <p>Waktu: {{ $kegiatanAktif->jam_mulai }} - {{ $kegiatanAktif->jam_selesai }}</p>
            </div>
        @else
            <div class="bg-gray-100 border-l-4 border-gray-500 text-gray-700 p-4 mb-4 rounded-lg mt-4" role="alert">
                <p>Tidak ada presensi aktif saat ini.</p>
            </div>
        @endif

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Pilih Kegiatan</h3>
            <form action="{{ route('presensi.submit') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="kegiatan_id" class="block text-sm font-medium text-gray-700">Kegiatan</label>
                    <select name="kegiatan_id" id="kegiatan_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Kegiatan</option>
                        @foreach ($kegiatan as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_kegiatan }} ({{ $item->tanggal }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="kode_unik" class="block text-sm font-medium text-gray-700">Kode unik</label>
                    <input type="number" name="kode_unik" id="kode_unik" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit Presensi</button>
            </form>
        </div>
    </div>
@endsection