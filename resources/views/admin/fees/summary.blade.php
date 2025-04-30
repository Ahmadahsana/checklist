@extends('layouts.vertical', ['title' => 'Admin Payment Summary'])

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "Admin", "title" => "Rekapan Pembayaran"])

    <div class="bg-white p-6 rounded-xl shadow-lg max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Rekapan Pembayaran User</h1>

        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="py-2 px-4 border-r">Nama User</th>
                    <th class="py-2 px-4 border-r">Biaya Tahunan</th>
                    <th class="py-2 px-4 border-r">Total Dibayar</th>
                    <th class="py-2 px-4 border-r">Status</th>
                    <th class="py-2 px-4 border-r">Angsuran Terbaru</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <?php
                        $totalPaid = $user->payments()->sum('amount');
                        $status = $totalPaid >= ($user->annual_fee ?? 0) ? 'Lunas' : 'Belum Lunas';
                    ?>
                    <tr class="border-b">
                        <td class="py-2 px-4 border-r">{{ $user->nama_lengkap }}</td>
                        <td class="py-2 px-4 border-r">Rp {{ number_format($user->annual_fee ?? 0, 0, ',', '.') }}</td>
                        <td class="py-2 px-4 border-r">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                        <td class="py-2 px-4 border-r">{{ $status }}</td>
                        <td class="py-2 px-4 border-r">
                            @if ($user->payments()->latest()->first())
                                Angsuran {{ $user->payments()->latest()->first()->installment_number }}: Rp {{ number_format($user->payments()->latest()->first()->amount, 0, ',', '.') }} ({{ $user->payments()->latest()->first()->status }})
                            @else
                                Belum Ada Pembayaran
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection