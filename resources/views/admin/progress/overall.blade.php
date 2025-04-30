@extends('layouts.vertical', ['title' => 'Admin Overall Progress'])

@section('css')
    <!-- Muat CSS ApexCharts melalui Vite -->
    @vite(['node_modules/apexcharts/dist/apexcharts.css'])
@endsection

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "Admin", "title" => "Progress Keseluruhan User"])

    <div class="bg-white p-6 rounded-xl shadow-lg max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Progress Keseluruhan User</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Chart Progress Keseluruhan -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Rata-rata Capaian Program</h2>
            <div id="overallProgressChart" class="h-64"></div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Muat JS ApexCharts, Lodash, dan Preline Helper melalui Vite -->
    @vite(['node_modules/lodash/lodash.min.js', 'node_modules/apexcharts/dist/apexcharts.min.js', 'node_modules/preline/dist/helper-apexcharts.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ambil data dari PHP menggunakan JSON.parse untuk memastikan format benar
            const overallProgress = {!! json_encode($overallProgress, JSON_THROW_ON_ERROR) !!} || {};
            const programs = {!! json_encode($programs->pluck('nama_program')->toArray(), JSON_THROW_ON_ERROR) !!} || [];

            // Pastikan data tidak kosong
            if (!programs.length) {
                console.warn('No programs found for overall progress chart.');
                return;
            }

            const seriesData = programs.map(program => overallProgress[program] || 0);

            const options = {
                chart: {
                    height: 250,
                    type: 'bar',
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Rata-rata Capaian (%)',
                    data: seriesData
                }],
                xaxis: {
                    categories: programs,
                    labels: {
                        style: {
                            colors: '#9ca3af',
                            fontSize: '13px',
                            fontFamily: 'Inter, ui-sans-serif',
                            fontWeight: 400
                        }
                    }
                },
                yaxis: {
                    min: 0,
                    max: 100,
                    tickAmount: 5,
                    labels: {
                        formatter: (value) => value + '%',
                        style: {
                            colors: '#9ca3af',
                            fontSize: '12px',
                            fontFamily: 'Inter, ui-sans-serif',
                            fontWeight: 400
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                colors: ['#2563EB'],
                grid: {
                    strokeDashArray: 0,
                    borderColor: '#e5e7eb',
                    padding: {
                        top: -20,
                        right: 0
                    }
                },
                tooltip: {
                    custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                        const program = w.globals.labels[dataPointIndex];
                        const value = series[seriesIndex][dataPointIndex];
                        return `<div class="p-2 bg-white shadow rounded-lg">
                            <span class="text-gray-800">${program}</span>: <span class="font-bold text-blue-600">${value}%</span>
                        </div>`;
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector('#overallProgressChart'), options);
            chart.render();
        });
    </script>
@endsection