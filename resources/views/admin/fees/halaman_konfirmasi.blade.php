@extends('layouts.vertical', ['title' => 'Validasi Pembayaran'])

@section('content')
    @include("layouts.shared.page-title", ["subtitle" => "Monitoring", "title" => "Validasi Pembayaran"])

    <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md">

        {{-- Alert Session --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        <h1 class="text-2xl font-bold mb-6">Detail Pembayaran</h1>

        <div class="space-y-4 text-sm text-gray-700">
            <div>
                <span class="font-semibold">Nama User:</span>
                <span>{{ $paymentDetail->payment->user->name ?? 'User Tidak Ditemukan' }}</span>
            </div>
            <div>
                <span class="font-semibold">Jumlah:</span>
                <span>Rp {{ number_format($paymentDetail->jumlah, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="font-semibold">Angsuran Ke:</span>
                <span>{{ $paymentDetail->angsuran_ke }}</span>
            </div>
            <div>
                <span class="font-semibold">Tanggal Pembayaran:</span>
                <span>{{ \Carbon\Carbon::parse($paymentDetail->tanggal)->format('d M Y, H:i') }}</span>
            </div>
            <div>
                <span class="font-semibold">Bukti Transfer:</span><br>
                @if($paymentDetail->bukti_tf)
                    <a href="{{ asset('storage/' . $paymentDetail->bukti_tf) }}" target="_blank">
                        <img src="{{ asset('storage/' . $paymentDetail->bukti_tf) }}" alt="Bukti Transfer" class="mt-2 w-64 rounded shadow">
                    </a>
                @else
                    <span class="text-gray-400">Tidak ada file</span>
                @endif
            </div>
        </div>

        <div class="mt-6 flex space-x-4">
            {{-- Tombol Validasi --}}
            <form method="POST" action="{{ route('admin.validasi-pembayaran', $paymentDetail->id) }}">
                @csrf
                @method('PUT')
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                    ✅ Validasi
                </button>
            </form>

            {{-- Tombol Tolak --}}
            <form method="POST" action="{{ route('admin.tolak-pembayaran', $paymentDetail->id) }}">
                @csrf
                @method('PUT')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                    ❌ Tolak
                </button>
            </form>

            <a href="{{ url()->previous() }}" class="ml-auto text-sm text-blue-600 hover:underline">← Kembali</a>
        </div>
    </div>
@endsection
