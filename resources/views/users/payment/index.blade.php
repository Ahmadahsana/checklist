@extends('layouts.vertical', ['title' => 'User Payment'])

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "User", "title" => "Pembayaran Angsuran"])

    <div class="bg-white p-6 rounded-xl shadow-lg max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Pembayaran Angsuran</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold">Detail Pembayaran</h3>
            <p>Biaya Yang Harus Dibayarkan: Rp {{ number_format($payments->harga_kos, 0, ',', '.') }}</p>
            <p>Total Dibayar: Rp {{ number_format($payments->total_terbayar, 0, ',', '.') }}</p>
            <p>Sisa Pembayaran: Rp {{ number_format($payments->kurang, 0, ',', '.') }}</p>
        </div>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold">Riwayat Angsuran</h3>
            {{-- @dd($payments->payment_details) --}}
            
            @if ($payments->payment_details->isEmpty())
                <p>Tidak ada riwayat angsuran.</p>
            @else
                <table class="w-full text-sm text-left text-default-500">
                    <thead class="text-xs text-default-700 uppercase bg-default-50 border-b">
                        <tr>
                            <th scope="col" class="px-6 py-3">Angsuran</th>
                            <th scope="col" class="px-6 py-3">Nominal</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments->payment_details as $payment)
                        <tr class="border-b hover:bg-default-50">
                            
                            <td class="px-6 py-4 text-default-900 whitespace-nowrap">
                                {{ $payment->angsuran_ke }}
                            </td>
                            <td class="px-6 py-4 text-default-900 whitespace-nowrap">
                                Rp {{ number_format($payment->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($payment->status === 'pending')
                                    <span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                        {{ $payment->status }}
                                    </span>
                                @elseif ($payment->status === 'diterima')
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                        {{ $payment->status }}
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                        {{ $payment->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-default-900 whitespace-nowrap">
                                {{ $payment->tanggal }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
        </div>

        <form action="{{ route('user.payment.submit') }}" method="POST" enctype="multipart/form-data" class="mt-6">
            @csrf
            <div class="mb-4">
                <label for="nominal" class="block text-sm font-medium text-gray-700">Nominal Pembayaran</label>
                <input type="number" name="nominal" id="nominal" class="w-full p-3 border border-gray-300 rounded-lg" step="1000" min="0" required>
            </div>
            <div class="mb-4">
                <label for="proof" class="block text-sm font-medium text-gray-700">Bukti Transfer</label>
                <input type="file" name="proof" id="proof" class="w-full p-3 border border-gray-300 rounded-lg" accept="image/*,application/pdf">
            </div>
            <button type="submit" class="bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700">Ajukan Pembayaran</button>
        </form>
    </div>
@endsection