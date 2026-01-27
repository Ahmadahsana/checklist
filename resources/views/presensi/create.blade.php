@extends('layouts.vertical', ['title' => 'Tambah Kegiatan'])

@section('content')
    @include("layouts.shared.page-title", ["subtitle" => "Monitoring", "title" => "Tambah Kegiatan"])

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Tambah Kegiatan Baru</h1>

        <form action="{{ route('presensi.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="tipe" class="block text-sm font-medium text-gray-700">Tipe Kegiatan</label>
                <select name="tipe" id="tipe" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="" disabled selected>Pilih Tipe</option>
                    <option value="insidental">Insidental</option>
                    <option value="rutin">Rutin</option>
                </select>
                @error('tipe')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="nama_kegiatan" class="block text-sm font-medium text-gray-700">Nama Kegiatan</label>
                <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('nama_kegiatan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4" id="tanggal-wrapper">
                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('tanggal')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 hidden" id="hari-wrapper">
                <label for="hari" class="block text-sm font-medium text-gray-700">Hari</label>
                <select name="hari" id="hari" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                    <option value="Minggu">Minggu</option>
                </select>
                @error('hari')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="jam_mulai" class="block text-sm font-medium text-gray-700">Jam Mulai</label>
                <input type="time" name="jam_mulai" id="jam_mulai" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('jam_mulai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="jam_akhir" class="block text-sm font-medium text-gray-700">Jam Akhir</label>
                <input type="time" name="jam_akhir" id="jam_akhir" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('jam_akhir')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tipeSelect = document.getElementById('tipe');
                const tanggalWrapper = document.getElementById('tanggal-wrapper');
                const hariWrapper = document.getElementById('hari-wrapper');
                const tanggalInput = document.getElementById('tanggal');
                const hariSelect = document.getElementById('hari');

                function toggleFields() {
                    if (tipeSelect.value === 'rutin') {
                        tanggalWrapper.classList.add('hidden');
                        hariWrapper.classList.remove('hidden');
                        tanggalInput.required = false;
                        hariSelect.required = true;
                    } else {
                        tanggalWrapper.classList.remove('hidden');
                        hariWrapper.classList.add('hidden');
                        tanggalInput.required = true;
                        hariSelect.required = false;
                    }
                }

                tipeSelect.addEventListener('change', toggleFields);
                toggleFields(); // Run on load
            });
        </script>
    </div>
@endsection