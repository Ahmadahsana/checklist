@extends('layouts.vertical', ['title' => 'Dashboard'])

@section('css')
    <!-- Muat CSS ApexCharts melalui Vite -->
    @vite(['node_modules/apexcharts/dist/apexcharts.css'])
@endsection

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "Apps", "title" => "Dashboard"])

    <div class="bg-white p-6 rounded-xl shadow-lg max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Dashboard Pribadi</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Progress Harian Keseluruhan -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Progress Harian Keseluruhan (Hari Ini)</h2>
            @if ($hasTargetsToday)
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $dailyProgress }}%"></div>
                </div>
                <p class="mt-1 text-sm text-gray-600">{{ $dailyProgress }}%</p>
            @else
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                    Anda belum mengisi target harian hari ini. Silakan isi di <a href="{{ route('user-targets.index') }}" class="text-blue-600 hover:underline">halaman target</a>.
                </div>
            @endif
        </div>

        <!-- Chart Keseluruhan -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Progress Tracking</h2>
                <div class="flex gap-2">
                    <button id="periodWeekly" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:bg-blue-700">Mingguan</button>
                    <button id="periodMonthly" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 focus:outline-none focus:bg-gray-300">6 Bulan</button>
                </div>
            </div>

            <!-- Legend Indicator -->
            <div class="flex justify-center sm:justify-end items-center gap-x-4 mb-3 sm:mb-6">
                <div class="inline-flex items-center">
                    <span class="size-2.5 inline-block bg-blue-600 rounded-sm me-2"></span>
                    <span class="text-[13px] text-gray-600 dark:text-neutral-400">
                        {{ $chartData['series'][0]['name'] ?? 'Target 1' }}
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="size-2.5 inline-block bg-cyan-500 rounded-sm me-2"></span>
                    <span class="text-[13px] text-gray-600 dark:text-neutral-400">
                        {{ $chartData['series'][1]['name'] ?? 'Target 2' }}
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="size-2.5 inline-block bg-gray-300 rounded-sm me-2 dark:bg-neutral-700"></span>
                    <span class="text-[13px] text-gray-600 dark:text-neutral-400">
                        {{ $chartData['series'][2]['name'] ?? 'Target 3' }}
                    </span>
                </div>
            </div>

            <!-- Apex Lines Chart -->
            <div id="overallChart" class="h-64"></div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Muat JS ApexCharts, Lodash, dan Preline Helper melalui Vite -->
    @vite(['node_modules/lodash/lodash.min.js', 'node_modules/apexcharts/dist/apexcharts.min.js', 'node_modules/preline/dist/helper-apexcharts.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = @json($chartData);
            let chart;

            function formatDate(dateStr, period) {
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) return dateStr; // Kembalikan asli jika parsing gagal

                const options = {
                    timeZone: 'Asia/Jakarta',
                    day: 'numeric',
                    month: 'short', // Gunakan singkatan bulan (Sep, Okt, dll.)
                    year: 'numeric'
                };

                if (period === 'weekly') {
                    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }); // Misalnya "6 Mar"
                } else { // monthly (6 bulan)
                    return date.toLocaleDateString('id-ID', { month: 'short' }); // Misalnya "Mar"
                }
            }

            function initializeChart(data, period = 'weekly') {
                if (chart) {
                    chart.destroy();
                }

                // Format categories ke bahasa Indonesia di frontend
                const formattedCategories = data.categories.map(category => {
                    if (period === 'monthly' && !isNaN(new Date(category))) {
                        // Jika category adalah tanggal (Y-m-d), format langsung
                        return formatDate(category, period);
                    } else if (period === 'monthly') {
                        // Jika category adalah nama bulan singkat (Sep, Oct, dll.), ubah ke format bahasa Indonesia
                        const monthMap = {
                            'Jan': 'Jan',
                            'Feb': 'Feb',
                            'Mar': 'Mar',
                            'Apr': 'Apr',
                            'May': 'Mei',
                            'Jun': 'Jun',
                            'Jul': 'Jul',
                            'Aug': 'Ags',
                            'Sep': 'Sep',
                            'Oct': 'Okt',
                            'Nov': 'Nov',
                            'Dec': 'Des'
                        };
                        return monthMap[category] || category; // Pastikan mapping ke bahasa Indonesia singkat
                    }
                    return formatDate(category, period);
                });

                const options = {
                    chart: {
                        height: 250,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    series: data.series.map((item, index) => ({
                        name: item.name,
                        data: item.data,
                        dashArray: item.dashArray || 0
                    })),
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight',
                        width: [4, 4, 4],
                        dashArray: data.series.map(item => item.dashArray)
                    },
                    title: {
                        show: false
                    },
                    legend: {
                        show: false
                    },
                    grid: {
                        strokeDashArray: 0,
                        borderColor: '#e5e7eb',
                        padding: {
                            top: -20,
                            right: 0
                        }
                    },
                    xaxis: {
                        type: 'category',
                        categories: formattedCategories, // Gunakan kategori yang sudah diformat
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        tooltip: {
                            enabled: false
                        },
                        labels: {
                            offsetY: 5,
                            style: {
                                colors: '#9ca3af',
                                fontSize: '13px',
                                fontFamily: 'Inter, ui-sans-serif',
                                fontWeight: 400
                            },
                            formatter: (title) => title  // Gunakan format yang sudah diformat
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        tickAmount: 5,
                        labels: {
                            align: 'left',
                            minWidth: 0,
                            maxWidth: 140,
                            style: {
                                colors: '#9ca3af',
                                fontSize: '12px',
                                fontFamily: 'Inter, ui-sans-serif',
                                fontWeight: 400
                            },
                            formatter: (value) => value + '%'
                        }
                    },
                    tooltip: {
                        
                        formatter: (value) => value + '%',
                        custom: function (props) {
                            const { categories } = props.ctx.opts.xaxis;
                            const { seriesIndex, dataPointIndex } = props;
                            const name = props.w.config.series[seriesIndex].name;
                            const value = props.series[seriesIndex][dataPointIndex];
                            const title = categories[dataPointIndex];

                            return buildTooltip(props, {
                                title: title,
                                mode: 'light',
                                hasTextLabel: true,
                                wrapperExtClasses: 'min-w-36',
                                labelDivider: ':',
                                labelExtClasses: 'ms-2'
                            });
                        }
                    },
                    colors: ['#2563EB', '#22D3EE', '#D1D5DB'],
                };

                chart = new ApexCharts(document.querySelector('#overallChart'), options);
                chart.render();
            }

            // Inisialisasi chart dengan data awal (weekly)
            initializeChart(chartData, 'weekly');

            // Event listener untuk tombol periode
            document.getElementById('periodWeekly').addEventListener('click', function () {
                updateChart('weekly');
            });

            document.getElementById('periodMonthly').addEventListener('click', function () {
                updateChart('monthly');
            });

            function updateChart(period) {
                const user = @json(Auth::user()->id); // Ambil ID user secara aman

                fetch(`/dashboard/update?period=${period}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status} - ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        alert(data.error); // Tampilkan pesan error spesifik dari server
                        return;
                    }
                    initializeChart(data.chartData, period);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memuat data chart. Silakan coba lagi. Detail: ' + error.message);
                });
            }

            // Debugging tambahan untuk memastikan data categories
            console.log('Initial Chart Data:', chartData);
        });
    </script>
@endsection