@extends('layouts.vertical', ['title' => 'Starter Page'])

@section('css')

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

    <div class="flex justify-end mb-4">
        <a href="{{ route('programs.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah Program</a>
    </div>

    <table id="programsTable" class="min-w-full bg-white table">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">No</th>
                <th class="py-2 px-4 border-b">Nama Program</th>
                <th class="py-2 px-4 border-b">Target</th>
                <th class="py-2 px-4 border-b">Satuan</th>
                <th class="py-2 px-4 border-b">Level</th>
                <th class="py-2 px-4 border-b">Jenis Isian</th>
                <th class="py-2 px-4 border-b">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($programs as $index => $program)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $index + 1 }}</td>
                    <td class="py-2 px-4 border-b">{{ $program->nama_program }}</td>
                    <td class="py-2 px-4 border-b">{{ $program->target }}</td>
                    <td class="py-2 px-4 border-b">{{ $program->unit }}</td>
                    <td class="py-2 px-4 border-b">{{ $program->level }}</td>
                    <td class="py-2 px-4 border-b">{{ $program->type }}</td>
                    <td class="py-2 px-4 border-b">
                        <a href="{{ route('programs.edit', $program) }}" class="text-blue-600 hover:underline">Edit</a>
                        
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new HSDataTable('#programsTable', {
            pageLength: 10,
            pagingOptions: {
                pageBtnClasses: 'min-w-[40px] flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none'
            }
        });
    });
</script>
@endsection