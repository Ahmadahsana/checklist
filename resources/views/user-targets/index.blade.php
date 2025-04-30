@extends('layouts.vertical', ['title' => 'Starter Page'])

@section('css')
@endsection

@section('content')
    @include("layouts.shared/page-title", ["subtitle" => "Apps", "title" => "Starter Page"])

    <div class="bg-white p-6 rounded-xl shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Target Harian</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Tanggal untuk Memuat Ulang Halaman -->
        <form method="GET" action="{{ route('user-targets.index') }}" id="dateForm" class="mb-6">
            @csrf
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Hari/Tanggal</label>
                <select name="date" id="date" class="w-full p-3 border border-gray-300 rounded-lg bg-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    @for ($i = 0; $i <= 7; $i++)
                        <?php $date = now()->subDays($i)->format('Y-m-d'); ?>
                        <option value="{{ $date }}" {{ $selectedDate === $date ? 'selected' : '' }}>
                            {{ now()->subDays($i)->translatedFormat('l, d F Y') }}
                        </option>
                    @endfor
                </select>
            </div>
        </form>

        <!-- Form Input Target -->
        <form method="POST" action="{{ route('user-targets.store') }}" id="targetForm" class="space-y-6">
            @csrf
            <input type="hidden" name="date" value="{{ $selectedDate }}">
            <div class="space-y-4">
                @foreach ($programs as $program)
                    <?php
                    // Debugging: Periksa apakah userTargets[$program->id] ada
                    $debugTarget = isset($userTargets[$program->id]) ? $userTargets[$program->id] : null;
                    \Log::info('Checking target for program ' . $program->id . ' on date ' . $selectedDate, ['target' => $debugTarget]);

                    // Hanya tandai sebagai completed jika ada data dengan status 'completed'
                    $isCompleted = false;
                    $value = null;
                    if (isset($userTargets[$program->id])) {
                        $target = $userTargets[$program->id];
                        if ($target->status === 'completed') {
                            $isCompleted = true;
                            $value = $target->value;
                        }
                    } else {
                        // Pastikan nilai default dari old() tidak mengganggu
                        $value = '';
                    }
                    ?>
                    <div class="flex items-center justify-between p-4 rounded-lg shadow-sm transition {{ $isCompleted ? 'bg-green-50 hover:bg-green-100' : 'bg-gray-50 hover:bg-gray-100' }}">
                        <a href="{{ route('user-targets.show', $program->id) }}" class="text-lg font-medium text-blue-600 hover:underline">{{ $program->nama_program }}</a>
                        @if ($program->type === 'boolean')
                            <div class="flex items-center">
                                <input type="hidden" name="targets[{{ $program->id }}][value]" value="0">
                                <input type="checkbox" name="targets[{{ $program->id }}][value]" id="program_{{ $program->id }}" value="1" class="hs-form-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500" {{ $isCompleted && $value == 1 ? 'checked' : '' }}>
                                <input type="hidden" name="targets[{{ $program->id }}][program_id]" value="{{ $program->id }}">
                            </div>
                        @else
                            <div class="flex items-center">
                                <input type="number" name="targets[{{ $program->id }}][value]" id="program_{{ $program->id }}" value="{{ $isCompleted ? $value : '' }}" class="w-24 p-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="0" step="0.01" required>
                                <span class="bg-gray-200 px-3 py-2 rounded-r-lg text-gray-700">{{ $program->unit }}</span>
                                <input type="hidden" name="targets[{{ $program->id }}][program_id]" value="{{ $program->id }}">
                            </div>
                        @endif
                        @error('targets.' . $program->id . '.value')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-semibold text-lg">Submit</button>
        </form>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Preline Checkbox (jika belum diatur di app.js)
            HSStaticMethods.autoInit();

            // Event listener untuk dropdown tanggal
            const dateSelect = document.getElementById('date');
            const dateForm = document.getElementById('dateForm');
            const targetForm = document.getElementById('targetForm');

            if (dateSelect && dateForm) {
                dateSelect.addEventListener('change', function () {
                    dateForm.submit(); // Submit form tanggal untuk reload halaman
                });
            }

            // Pastikan form target bisa disubmit
            if (targetForm) {
                targetForm.addEventListener('submit', function (e) {
                    e.preventDefault(); // Cegah submit default untuk debugging
                    console.log('Form target submitted:', new FormData(targetForm));
                    targetForm.submit(); // Submit form target
                });
            }

            // Debugging tambahan untuk memastikan form ada
            console.log('Date Form:', dateForm);
            console.log('Target Form:', targetForm);
        });
    </script>
@endsection