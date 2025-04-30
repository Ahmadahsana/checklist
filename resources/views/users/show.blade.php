@extends('layouts.vertical', ['title' => 'Detail User'])

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "Apps", "title" => "Detail User"])

    <div class="bg-white p-6 rounded-lg shadow-md max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Detail User: {{ $user->nama_lengkap }}</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <p class="mt-1 text-sm text-gray-800">{{ $user->username }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <p class="mt-1 text-sm text-gray-800">{{ $user->nama_lengkap }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Level</label>
                <p class="mt-1 text-sm text-gray-800">{{ $user->level ?? '-' }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <p class="mt-1 text-sm text-gray-800">{{ $user->role }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <p class="mt-1 text-sm text-gray-800">{{ $user->email }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">No HP</label>
                <p class="mt-1 text-sm text-gray-800">{{ $user->no_hp }}</p>
            </div>

            <!-- Tambahkan field lain jika diperlukan, seperti alamat, prodi, dll. -->

            <div class="col-span-2 mt-6">
                <form method="POST" action="{{ route('users.reset.password', $user) }}">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Reset Password</button>
                    <a href="{{ route('users.index') }}" class="ml-2 text-gray-600 hover:underline">Kembali ke Daftar User</a>
                </form>
            </div>
        </div>
    </div>
@endsection