@extends('layouts.vertical', ['title' => 'Admin Manage User Fee'])

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "Admin", "title" => "Detail Biaya User"])

    <div class="bg-white p-6 rounded-xl shadow-lg max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Detail Biaya User: {{ $user->nama_lengkap }} </h1>
        {{-- @dd($user) --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold">Informasi User</h3>
            <p>Program: {{ $user->level }}</p>
            <p>Tarif: Rp {{ number_format($user->harga_kos ?? 0, 0, ',', '.') }}</p>
        </div>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold">Riwayat Angsuran</h3>
            @if ($user->payments->payment_details->isEmpty())
                <p>Tidak ada riwayat angsuran.</p>
            @else
                {{-- <ul>
                    @foreach ($user->payments->payment_details as $payment)
                        <li>Angsuran {{ $payment->installment_number }}: Rp {{ number_format($payment->amount, 0, ',', '.') }} ({{ $payment->status }}, Tanggal: {{ $payment->payment_date }}, Batas Waktu: {{ $payment->due_date }})</li>
                    @endforeach
                </ul> --}}
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
                        @foreach ($user->payments->payment_details as $payment)
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
                                @elseif ($payment->status === 'berhasil')
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

        {{-- <form action="{{ route('admin.manage-fees.update') }}" method="POST" class="mt-2">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <div class="flex gap-2">
                <input type="number" name="annual_fee" value="{{ $user->annual_fee ?? 0 }}" class="w-1/2 p-2 border border-gray-300 rounded-lg" step="1000" min="0" required>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">Update Biaya</button>
            </div>
        </form>

        <form action="{{ route('admin.manage-fees.set-due-date') }}" method="POST" class="mt-4">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <div class="flex gap-2">
                <input type="number" name="installment_number" placeholder="Nomor Angsuran" class="w-1/4 p-2 border border-gray-300 rounded-lg" min="1" required>
                <input type="date" name="due_date" class="w-1/2 p-2 border border-gray-300 rounded-lg" required>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">Set Batas Waktu</button>
            </div>
        </form> --}}

        <div class="mt-6">
            <a href="{{ route('admin.manage-fees') }}" class="bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700">Kembali</a>
        </div>
    </div>
@endsection