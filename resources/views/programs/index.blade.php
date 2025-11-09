@extends('layouts.vertical', ['title' => 'Starter Page'])

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
@include("layouts.shared/page-title", ["subtitle" => "Apps", "title" => "Starter Page"])

<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Daftar Program</h1>

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

    <div class="flex justify-end mb-4">
        <a href="{{ route('programs.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah Program</a>
    </div>

    <div class="overflow-hidden">
        <table id="programsTable" class="min-w-full bg-white divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Nama Program</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Target</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Satuan</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Level</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Jenis Isian</th>
                    <th class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($programs as $index => $program)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $program->nama_program }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $program->target }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $program->unit }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $program->level }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $program->type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                            <a href="{{ route('programs.edit', $program) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('programs.destroy', $program) }}" method="POST" class="inline-block delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline ml-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data program.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi SweetAlert2 untuk konfirmasi hapus
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Program ini akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection