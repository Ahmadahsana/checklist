<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Rumah Prestasi Mahasiswa </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
    <meta content="MyraStudio" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="/images/favicon.ico">
    @vite(['resources/css/icons.css', 'resources/css/app.css',])
    @vite(['resources/js/app.js'])

    </head>

<body>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Pendaftaran User</h1>
    
                @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="/daftar_baru" id="userForm">
            @csrf
            {{-- <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required autocomplete="off">
                @error('username')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div> --}}
            <div class="mb-4">
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('nama_lengkap') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="level" class="block text-sm font-medium text-gray-700">Program</label>
                <select name="level" id="level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    
                    @if ($level == 'rumahtahfidzmahasiswa')
                    <option value="tahfidz">Tahfidz</option>
                    @else
                    <option value="regular">Regular</option>
                    @endif
                </select>
                @error('level')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            
    
            <div class="mb-4">
                <label for="nama_panggilan" class="block text-sm font-medium text-gray-700">Nama Panggilan</label>
                <input type="text" name="nama_panggilan" id="nama_panggilan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('nama_panggilan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="tempat_lahir" class="block text-sm font-medium text-gray-700">Kota kelahiran</label>
                <input type="text" name="tempat_lahir" id="tempat_lahir" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('tempat_lahir')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('tanggal_lahir')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="alamat_asal" class="block text-sm font-medium text-gray-700">Alamat Asal</label>
                <input type="text" name="alamat_asal" id="alamat_asal" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('alamat_asal')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="no_hp" class="block text-sm font-medium text-gray-700">No HP</label>
                <input type="tel" min="11" name="no_hp" id="no_hp" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('no_hp')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="prodi" class="block text-sm font-medium text-gray-700">Program Studi</label>
                <input type="text" name="prodi" id="prodi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('prodi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="fakultas" class="block text-sm font-medium text-gray-700">Fakultas</label>
                <input type="text" name="fakultas" id="fakultas" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('fakultas')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="angkatan" class="block text-sm font-medium text-gray-700">Angkatan</label>
                <input type="number" name="angkatan" id="angkatan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('angkatan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="nama_ayah" class="block text-sm font-medium text-gray-700">Nama Ayah</label>
                <input type="text" name="nama_ayah" id="nama_ayah" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('nama_ayah')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="nama_ibu" class="block text-sm font-medium text-gray-700">Nama Ibu</label>
                <input type="text" name="nama_ibu" id="nama_ibu" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('nama_ibu')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="no_hp_ortu" class="block text-sm font-medium text-gray-700">No.HP ortu</label>
                <input type="tel" min="11" name="no_hp_ortu" id="no_hp_ortu" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('no_hp_ortu')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="no_hp_saudara_wali" class="block text-sm font-medium text-gray-700">No.HP saudara / wali</label>
                <input type="tel" min="11" name="no_hp_saudara_wali" id="no_hp_saudara_wali" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('no_hp_saudara_wali')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="nama_kos" class="block text-sm font-medium text-gray-700">Nama Kos</label>
                <select name="nama_kos" id="nama_kos" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="" disabled selected>Pilih kos</option>
                    @foreach ($kos as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_kos }}</option>
                    @endforeach
                </select>
                @error('nama_kos')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                {{-- <input type="text" name="nama_kos" id="nama_kos" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"> --}}
            </div>

            {{-- <div class="mb-4">
                <label for="harga_kos" class="block text-sm font-medium text-gray-700">Harga Kos</label>
                <input type="number" name="harga_kos" id="harga_kos" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('harga_kos')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div> --}}
            <div class="mb-4">
                <label for="harga_kos_mask" class="block text-sm font-medium text-gray-700">Harga Kos</label>
                
                <!-- Inputan yang terlihat oleh pengguna (Masked) -->
                <input type="text" id="harga_kos_mask" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                
                <!-- Input Hidden untuk menyimpan angka murni (Raw) -->
                <input type="hidden" name="harga_kos" id="harga_kos_raw">
                
                @error('harga_kos')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
    
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
            {{-- <a href="{{ route('users.index') }}" class="ml-2 text-gray-600 hover:underline">Kembali</a> --}}
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>

    

    <script>
        $(document).ready(function() {
            // Terapkan Input Mask pada input yang terlihat
            $("#harga_kos_mask").inputmask({
                alias: "currency",
                prefix: "Rp. ",
                groupSeparator: ".",
                radixPoint: ",",
                autoGroup: true,
                digits: 0,
                rightAlign: false
            });
    
            // Saat input berubah, update input hidden dengan angka murni
            $("#harga_kos_mask").on("input", function() {
                let rawValue = $(this).inputmask("unmaskedvalue"); // Ambil angka murni
                $("#harga_kos_raw").val(rawValue); // Simpan ke input hidden
            });
        });
    </script>

</body>

</html>