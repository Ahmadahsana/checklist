@extends('layouts.vertical', ['title' => 'Presensi User'])

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "User", "title" => "Presensi Kegiatan"])

    <div class="bg-white p-6 rounded-xl shadow-lg max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Formulir Presensi</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('user.presensi.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="kegiatan_id" class="block text-sm font-medium text-gray-700">Pilih Kegiatan</label>
                <select name="kegiatan_id" id="kegiatan_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach ($kegiatans as $kegiatan)
                        <option value="{{ $kegiatan->id }}">{{ $kegiatan->nama_kegiatan }} ({{ $kegiatan->tanggal }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="kode_unik" class="block text-sm font-medium text-gray-700">Kode Kehadiran</label>
                <input type="number" name="kode_unik" id="kode_unik" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <p class="mt-1 text-sm text-gray-500">Kode diberikan oleh pengelola saat acara berlangsung.</p>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Kirim Presensi</button>
        </form>
    </div>
@endsection