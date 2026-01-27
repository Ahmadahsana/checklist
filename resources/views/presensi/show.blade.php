@extends('layouts.vertical', ['title' => 'Detail Presensi Kegiatan'])

@section('content')
    @include('layouts.shared.page-title', ['subtitle' => 'Monitoring', 'title' => 'Detail Presensi Kegiatan'])

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">{{ $kegiatan->nama_kegiatan }}</h1>
        @if ($kegiatan->jenis === 'rutin')
            <p class="text-sm text-gray-600 mb-2">Hari: {{ $kegiatan->hari }}</p>
        @else
            <p class="text-sm text-gray-600 mb-2">Tanggal: {{ $kegiatan->tanggal }}</p>
        @endif
        <p class="text-sm text-gray-600 mb-2">Tipe: {{ ucfirst($kegiatan->jenis) }}</p>
        @if ($kegiatan->jenis === 'rutin')
            <div class="mb-4">
                <span class="text-sm text-gray-600 block mb-1">Kode Presensi (Minggu Ini):</span>
                <span class="text-2xl font-mono font-bold bg-blue-100 text-blue-800 px-3 py-1 rounded">{{ $kegiatan->kode_mingguan }}</span>
                <p class="text-xs text-gray-500 mt-1">Kode ini berubah setiap minggu secara otomatis.</p>
            </div>
        @else
            <p class="text-sm text-gray-600 mb-4">Kode Presensi: <span class="font-mono font-bold">{{ $kegiatan->kode_unik }}</span></p>
        @endif

        <div class="mb-6">
            <h3 class="text-lg font-semibold">Statistik Kehadiran</h3>
            <p>Total Peserta: {{ $totalPeserta }}</p>
            <p>Hadir: {{ $hadir }} ({{ $persentaseKehadiran }}%)</p>
            <p>Tidak Hadir: {{ $tidakHadir }} ({{ $persentaseKetidakhadiran }}%)</p>
        </div>

        @if ($kegiatan->jenis === 'insidental')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Peserta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($presensis as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->user->nama_lengkap ?? $item->user->username ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $item->hadir ? 'Hadir' : 'Tidak hadir' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data presensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        @if ($kegiatan->jenis === 'rutin')
            <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Rekap Kehadiran Bulanan</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('presensi.export', ['kegiatan' => $kegiatan->id, 'month' => $selectedMonth, 'year' => $selectedYear]) }}" 
                           class="bg-green-600 text-white px-3 py-2 rounded-md text-sm hover:bg-green-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Export CSV
                        </a>
                        <form action="{{ route('presensi.show', $kegiatan->id) }}" method="GET" class="flex gap-2">
                        <select name="month" class="border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" class="border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                            @foreach(range(date('Y') - 1, date('Y') + 1) as $y)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    </div>
                </div>

                @if (!empty($weeklyRecap))
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border border-gray-200 px-4 py-2 text-center font-semibold uppercase text-gray-600">No</th>
                                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold uppercase text-gray-600">Nama</th>
                                    @foreach ($weeklyHeaders as $header)
                                        <th class="border border-gray-200 px-4 py-2 text-center font-semibold uppercase text-gray-600">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($weeklyRecap as $index => $row)
                                    <tr class="odd:bg-white even:bg-gray-50">
                                        <td class="border border-gray-200 px-4 py-2 text-center">{{ $index + 1 }}</td>
                                        <td class="border border-gray-200 px-4 py-2">{{ $row['user']->nama_lengkap ?? $row['user']->username }}</td>
                                        @foreach ($row['statuses'] as $status)
                                            <td class="border border-gray-200 px-4 py-2 text-center text-lg">
                                                <span class="{{ $status === '✔' ? 'text-green-600' : ($status === '✘' ? 'text-red-500' : 'text-gray-400') }}">{{ $status }}</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 italic mt-2">Tidak ada data kegiatan di bulan ini.</p>
                @endif
            </div>
        @endif

        @if ($kegiatan->jenis === 'insidental' && ($belumHadirUsers->isNotEmpty() || $presensiTidakHadir->isNotEmpty()))
            <div class="mt-8">
                <h3 class="text-lg font-semibold mb-4">Belum / Tidak Hadir</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @php $row = 1; @endphp
                            @foreach ($presensiTidakHadir as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $row++ }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->user->nama_lengkap ?? $item->user->username ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">Tidak hadir</td>
                                </tr>
                            @endforeach
                            @foreach ($belumHadirUsers as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $row++ }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->nama_lengkap ?? $user->username ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600">Belum hadir</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
