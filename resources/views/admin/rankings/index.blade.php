@extends('layouts.vertical', ['title' => 'Ranking User'])

@section('css')

@endsection

@section('content')

@include("layouts.shared/page-title", ["subtitle" => "Apps", "title" => "Ranking User"])

<div class="bg-white p-6 rounded-lg shadow-md max-w-4xl mx-auto">
    <!-- Form untuk memilih bulan -->
    <form method="GET" action="{{ route('admin.rank') }}" class="mb-6">
        <div class="flex items-center space-x-4">
            <label for="bulan" class="text-gray-700 font-medium">Pilih Bulan:</label>
            <select name="bulan" id="bulan" class="form-select rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" onchange="this.form.submit()">
                @foreach ($availableBulan as $bulan)
                    <option value="{{ $bulan }}" {{ $bulan == $selectedBulan ? 'selected' : '' }}>
                        {{ date('F Y', strtotime($bulan . '-01')) }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <!-- Tabel perangkingan -->
    @if ($rankings->isEmpty())
        <div class="text-center text-gray-500 py-4">
            Tidak ada data progres untuk bulan {{ date('F Y', strtotime($selectedBulan . '-01')) }}.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left text-gray-700 font-semibold">Peringkat</th>
                        <th class="px-4 py-2 text-left text-gray-700 font-semibold">Nama</th>
                        <th class="px-4 py-2 text-left text-gray-700 font-semibold">Persentase Pencapaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rankings as $ranking)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-600">{{ $ranking->rank }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $ranking->nama_lengkap }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ number_format($ranking->value, 2) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection

@section('script')

@endsection