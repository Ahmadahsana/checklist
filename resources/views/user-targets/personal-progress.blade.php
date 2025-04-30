@extends('layouts.vertical', ['title' => 'Progress Personal'])

@section('css')
    <!-- Muat CSS ApexCharts melalui Vite -->
    @vite(['node_modules/apexcharts/dist/apexcharts.css'])
@endsection

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "Apps", "title" => "Progress Personal"])

    <div class="bg-white p-6 rounded-xl shadow-lg max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Progress Pribadi</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Statistik Tambahan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800">Rata-rata Keberhasilan {{ $period === 'weekly' ? 'Minggu Ini' : '6 Bulan Terakhir' }}</h3>
                <p class="text-xl font-bold text-blue-600">{{ $overallStats['averageProgress'] }}%</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800">Target Selesai {{ $period === 'weekly' ? 'Minggu Ini' : '6 Bulan Terakhir' }}</h3>
                <p class="text-xl font-bold text-blue-600">{{ $overallStats['completedTargets'] }} dari {{ $overallStats['totalPrograms'] }} ({{ $overallStats['completedPercentage'] }}%)</p>
            </div>
        </div>

        <!-- Dropdown untuk Memilih Target dan Periode -->
        <div class="mb-6 flex gap-4 items-center">
            <select id="programSelect" class="w-1/2 p-3 border border-gray-300 rounded-lg bg-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @foreach ($programs as $program)
                    <option value="{{ $program->id }}" {{ $selectedProgram && $selectedProgram->id === $program->id ? 'selected' : '' }}>
                        {{ $program->nama_program }} ({{ $program->target }} {{ $program->unit }})
                    </option>
                @endforeach
            </select>

            <select id="periodSelect" class="w-1/2 p-3 border border-gray-300 rounded-lg bg-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Bulanan</option>
            </select>
        </div>

        <!-- Chart Presentase Keberhasilan -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Presentase Keberhasilan</h2>
            <div id="progressChart" class="h-64"></div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Muat JS ApexCharts, Lodash, dan Preline Helper melalui Vite -->
    @vite(['node_modules/lodash/lodash.min.js', 'node_modules/apexcharts/dist/apexcharts.min.js', 'node_modules/preline/dist/helper-apexcharts.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = @json($chartData);
            console.log('Chart Data from Server:', chartData); // Debugging
            let chart;

            function initializeChart(data, period = 'weekly') {
                if (chart) {
                    chart.destroy();
                }

                const options = {
                    chart: {
                        height: 250,
                        type: 'line',
                        toolbar: {
                            show: true
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    series: [{
                        name: 'Presentase Keberhasilan',
                        data: data.series[0], // Data sudah dalam persentase
                    }],
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight',
                        width: [4],
                        dashArray: [0]
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
                        categories: data.categories,
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
                            formatter: (title) => {
                                let t = title;
                                if (period === 'weekly') {
                                    if (t) {
                                        const newT = t.split(', ');
                                        t = newT[1].slice(0, 3) + ' ' + newT[0].slice(0, 3); // Misalnya: "Feb Thu"
                                    }
                                } else { // monthly (6 bulan)
                                    t = t; // Nama bulan sudah dalam format singkat (Sep, Oct, dll.)
                                }
                                return t;
                            }
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        tickAmount: 5,
                        labels: {
        formatter: function (value) {
            return value.toFixed(2) + '%'; // Pastikan ini digunakan
        }
    }
                    },
                    tooltip: {
                        y: {
                            formatter: function (value) {
                                return value + '%'; // Format tooltip sebagai persentase
                            }
                        },
                        custom: function (props) {
                            const { categories } = props.ctx.opts.xaxis;
                            const { dataPointIndex } = props;
                            const title = categories[dataPointIndex];
                            let newTitle = title;

                            if (period === 'weekly') {
                                const newT = title.split(', ');
                                newTitle = `${newT[1].slice(0, 3)} ${newT[0].slice(0, 3)}`; // Misalnya: "Feb Thu"
                            } else { // monthly (6 bulan)
                                newTitle = title; // Nama bulan (Sep, Oct, dll.)
                            }

                            return buildTooltip(props, {
                                title: newTitle,
                                mode: 'light',
                                hasTextLabel: true,
                                wrapperExtClasses: 'min-w-36',
                                labelDivider: '=',
                                labelExtClasses: 'ms-2'
                            });
                        }
                    },
                    colors: ['#2563EB'],
                };

                chart = new ApexCharts(document.querySelector('#progressChart'), options);
                chart.render();
            }

            // Inisialisasi chart dengan data awal (weekly)
            initializeChart(chartData, 'weekly');

            // Event listener untuk dropdown
            document.getElementById('programSelect').addEventListener('change', function () {
                updateChart();
            });

            document.getElementById('periodSelect').addEventListener('change', function () {
                updateChart();
            });

            function updateChart() {
                const programId = document.getElementById('programSelect').value;
                const period = document.getElementById('periodSelect').value;

                fetch(`/progress-personal/update?program_id=${programId}&period=${period}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Respons server tidak valid');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Updated Chart Data:', data.chartData); // Debugging
                    if (data.error) {
                        console.error('Error:', data.error);
                        alert('Terjadi kesalahan saat memuat data chart. Silakan coba lagi.');
                        return;
                    }
                    initializeChart(data.chartData, period);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memuat data chart. Silakan coba lagi.');
                });
            }
        });
    </script>
@endsection