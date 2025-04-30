@extends('layouts.vertical', ['title' => 'Detail Target'])

@section('css')
    <!-- Muat CSS ApexCharts melalui Vite -->
    @vite(['node_modules/apexcharts/dist/apexcharts.css'])
@endsection

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "Apps", "title" => "Detail Target"])

    <div class="bg-white p-6 rounded-xl shadow-lg max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">{{ $program->nama_program }}</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Chart Presentase Keberhasilan -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Presentase Keberhasilan (7 Hari Terakhir)</h2>
            <div id="targetChart" class="h-64"></div>
        </div>

        <a href="{{ route('user-targets.index') }}" class="mt-6 inline-block bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-semibold text-lg">Kembali</a>
    </div>
@endsection

@section('script')
    <!-- Muat JS ApexCharts, Lodash, dan Preline Helper melalui Vite -->
    @vite(['node_modules/lodash/lodash.min.js', 'node_modules/apexcharts/dist/apexcharts.min.js', 'node_modules/preline/dist/helper-apexcharts.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = @json($chartData);

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
                series: [{
                    name: 'Presentase Keberhasilan',
                    data: chartData.series[0],
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
                    categories: chartData.categories,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    tooltip: {
                        y: {
        formatter: function (val) {
            return val + '%';
        }
    }
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

                            if (t) {
                                const newT = t.split(' ');
                                t = `${newT[0]} ${newT[1].slice(0, 3)}`; // Misalnya: "Senin, 20 Feb"
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
                        const { dataPointIndex } = props;
                        const title = categories[dataPointIndex].split(' ');
                        const newTitle = `${title[0]} ${title[1].slice(0, 3)}`;

                        return buildTooltip(props, {
                            title: newTitle,
                            mode: 'light',
                            hasTextLabel: true,
                            wrapperExtClasses: 'min-w-28',
                            labelDivider: ':',
                            labelExtClasses: 'ms-2'
                        });
                    }
                },
                colors: ['#2563EB'],
            };

            const chart = new ApexCharts(document.querySelector('#targetChart'), options);
            chart.render();
        });
    </script>
@endsection