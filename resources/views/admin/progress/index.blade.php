@extends('layouts.vertical', ['title' => 'Admin Progress'])

@section('css')
    <!-- Muat CSS ApexCharts melalui Vite -->
    @vite(['node_modules/apexcharts/dist/apexcharts.css'])
@endsection

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "Admin", "title" => "Progress User"])

    <div class="bg-white p-6 rounded-xl shadow-lg max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Progress User</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Pencarian User -->
        <div class="mb-6">
            <label for="userSelect" class="block text-sm font-medium text-gray-700 mb-2">Pilih User</label>
            <select id="userSelect" class="w-full p-3 border border-gray-300 rounded-lg bg-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                <option value="">Pilih User</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} (Level: {{ $user->level }})</option>
                @endforeach
            </select>
        </div>

        <!-- Dropdown Periode -->
        <div class="mb-6 flex gap-4 items-center">
            <select id="periodSelect" class="w-1/2 p-3 border border-gray-300 rounded-lg bg-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="weekly">Mingguan</option>
                <option value="monthly">6 Bulan</option>
            </select>
        </div>

        <!-- Chart Progress -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Progress Tracking</h2>
            <div id="progressChart" class="h-64"></div>
        </div>

        <!-- Tombol untuk Progress Keseluruhan -->
        <div class="mt-6">
            <a href="{{ route('admin.progress.overall') }}" class="bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-semibold text-lg">
                Lihat Progress Keseluruhan User
            </a>
        </div>
    </div>
@endsection

@section('script')
    <!-- Muat JS ApexCharts, Lodash, dan Preline Helper melalui Vite -->
    @vite(['node_modules/lodash/lodash.min.js', 'node_modules/apexcharts/dist/apexcharts.min.js', 'node_modules/preline/dist/helper-apexcharts.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let chart;
            const userSelect = document.getElementById('userSelect');
            const periodSelect = document.getElementById('periodSelect');

            function formatDate(dateStr, period) {
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) {
                    // Jika dateStr adalah nama bulan singkat (Sep, Oct, dll.), mapping ke bahasa Indonesia
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
                    return monthMap[dateStr] || dateStr; // Kembalikan nama bulan singkat dalam bahasa Indonesia
                }

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
                const formattedCategories = data.categories.map(category => formatDate(category, period));

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
                    series: data.series,
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight',
                        width: [4, 4, 4],
                        dashArray: data.series.map(item => item.dashArray || 0)
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
                            formatter: (title) => title // Gunakan format yang sudah diformat
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

                chart = new ApexCharts(document.querySelector('#progressChart'), options);
                chart.render();
            }

            // Inisialisasi chart dengan data default (kosong atau placeholder)
            initializeChart({ categories: [], series: [] }, 'weekly');

            // Event listener untuk dropdown
            userSelect.addEventListener('change', function () {
                if (this.value) {
                    updateChart(this.value, periodSelect.value);
                }
            });

            periodSelect.addEventListener('change', function () {
                if (userSelect.value) {
                    updateChart(userSelect.value, this.value);
                }
            });

            function updateChart(userId, period) {
                fetch(`/admin/progress/update?user_id=${userId}&period=${period}`, {
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
                        alert(data.error);
                        return;
                    }
                    initializeChart(data.chartData, period);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memuat data chart. Silakan coba lagi. Detail: ' + error.message);
                });
            }
        });
    </script>
@endsection