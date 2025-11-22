@extends('layouts.vertical', ['title' => 'Daftar User'])

@section('css')
@endsection

@section('content')

@include("layouts.shared/page-title", ["subtitle" => "Apps", "title" => "Daftar User"])

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar User</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('users.export') }}"
                class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Export Excel</a>
            <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah User</a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-2">
                <label for="usersTableLength" class="text-sm font-medium text-gray-700">Tampilkan</label>
                <select id="usersTableLength" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-sm text-gray-600">per halaman</span>
            </div>
            <div class="w-full md:max-w-xs">
                <label for="usersTableSearch" class="sr-only">Cari user</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="usersTableSearch" class="block w-full rounded-lg border-gray-300 pl-9 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Cari pengguna...">
                </div>
            </div>
        </div>

        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="overflow-hidden border border-gray-100 rounded-lg">
                    <table id="usersTable" class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <caption class="py-2 text-start text-sm text-gray-600 dark:text-neutral-500">List of users</caption>
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Nomor</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Username</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Nama Lengkap</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Program</th>
                                <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @foreach ($users as $user)
                            <tr>
                                <td data-column="number" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $user->username }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $user->nama_lengkap }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $user->level ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                    <a href="{{ route('users.show', $user) }}" class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 focus:outline-none focus:text-blue-800 disabled:opacity-50 disabled:pointer-events-none dark:text-blue-500 dark:hover:text-blue-400 dark:focus:text-blue-400">Detail</a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block delete-form" >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-red-600 hover:text-red-800 focus:outline-none focus:text-red-800 disabled:opacity-50 disabled:pointer-events-none dark:text-red-500 dark:hover:text-red-400 dark:focus:text-red-400">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <p id="usersTableInfo" class="text-sm text-gray-600">Menampilkan 0 data</p>
            <div id="usersTablePagination" class="flex flex-wrap items-center gap-2"></div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'User ini akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        const table = document.getElementById('usersTable');
        const searchInput = document.getElementById('usersTableSearch');
        const pageLengthSelect = document.getElementById('usersTableLength');
        const paginationContainer = document.getElementById('usersTablePagination');
        const infoText = document.getElementById('usersTableInfo');

        if (!table || !searchInput || !pageLengthSelect || !paginationContainer || !infoText) {
            return;
        }

        const rows = Array.from(table.querySelectorAll('tbody tr'));
        let currentPage = 1;

        const getFilteredRows = () => {
            const query = searchInput.value.trim().toLowerCase();
            if (!query) {
                return rows;
            }
            return rows.filter((row) => row.textContent.toLowerCase().includes(query));
        };

        const renderPagination = (totalPages) => {
            paginationContainer.innerHTML = '';

            const createButton = (label, page, disabled = false, active = false) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.className = [
                    'px-3 py-1 rounded-full border text-sm',
                    active
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'text-gray-700 border-gray-200 hover:bg-gray-100',
                    disabled ? 'opacity-50 cursor-not-allowed' : ''
                ].join(' ');
                button.disabled = disabled;
                button.addEventListener('click', () => {
                    if (disabled) return;
                    currentPage = page;
                    renderTable();
                });
                paginationContainer.appendChild(button);
            };

            createButton('Sebelumnya', Math.max(1, currentPage - 1), currentPage === 1);

            const maxButtons = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = startPage + maxButtons - 1;

            if (endPage > totalPages) {
                endPage = totalPages;
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            for (let page = startPage; page <= endPage; page++) {
                createButton(page, page, false, page === currentPage);
            }

            createButton('Berikutnya', Math.min(totalPages, currentPage + 1), currentPage === totalPages);
        };

        const renderTable = () => {
            const filteredRows = getFilteredRows();
            const perPage = parseInt(pageLengthSelect.value, 10);
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / perPage));

            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            const start = (currentPage - 1) * perPage;
            const visibleRows = filteredRows.slice(start, start + perPage);

            rows.forEach((row) => {
                row.style.display = 'none';
            });

            visibleRows.forEach((row, index) => {
                row.style.display = '';
                const numberCell = row.querySelector('[data-column=\"number\"]');
                if (numberCell) {
                    numberCell.textContent = start + index + 1;
                }
            });

            const startNumber = filteredRows.length ? start + 1 : 0;
            const endNumber = start + visibleRows.length;
            infoText.textContent = `Menampilkan ${startNumber}-${endNumber} dari ${filteredRows.length} pengguna`;

            renderPagination(totalPages);
        };

        searchInput.addEventListener('input', () => {
            currentPage = 1;
            renderTable();
        });

        pageLengthSelect.addEventListener('change', () => {
            currentPage = 1;
            renderTable();
        });

        renderTable();
    });
</script>
@endsection
