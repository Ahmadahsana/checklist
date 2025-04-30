@extends('layouts.vertical', ['title' => 'Admin Manage Fees'])

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "Admin", "title" => "Kelola Biaya User"])

    <div class="bg-white p-6 rounded-xl shadow-lg w-full  mx-auto">
        <div class="flex justify-between">
            <h1 class="text-2xl font-bold mb-4 text-gray-800">Kelola Biaya User</h1>
            
        </div>
        {{-- <h1 class="text-3xl font-bold mb-6 text-gray-800">Kelola Biaya User</h1> --}}

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="py-2 px-4 border-r">No.</th>
                    <th class="py-2 px-4 border-r">Nama User</th>
                    <th class="py-2 px-4 border-r">Nama kos</th>
                    <th class="py-2 px-4 border-r">Bayar</th>
                    <th class="py-2 px-4 border-r">tanggal</th>
                    <th class="py-2 px-4 border-r">Status</th>
                    <th class="py-2 px-4 border-r">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pembayaran_masuk as $pembayaran)
                    <tr class="border-b">
                        <td class="py-2 px-4 border-r">{{ $loop->iteration }}</td>
                        <td class="py-2 px-4 border-r">{{ $pembayaran->payment->user->nama_lengkap }}</td>
                        <td class="py-2 px-4 border-r">{{ $pembayaran->payment->user->kos->nama_kos }}</td>
                        <td class="py-2 px-4 border-r">Rp {{ number_format($pembayaran->jumlah ?? 0, 0, ',', '.') }}</td>
                        <td class="py-2 px-4 border-r">{{ $pembayaran->tanggal }}</td>
                        <td class="py-2 px-4 border-r">
                            {{-- {{ $pembayaran->payments->status }} --}}
                            @if ($pembayaran->status === 'pending')
                                <span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                    {{ $pembayaran->status }}
                                </span>
                            @elseif ($pembayaran->status === 'berhasil')
                                <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                    {{ $pembayaran->status }}
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                    {{ $pembayaran->status }}
                                </span>
                            @endif
                        </td>
                        <td class="py-2 px-4 border-r">
                            <a href="{{ route('admin.konfirmasi', $pembayaran->id) }}" class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700">Cek</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection