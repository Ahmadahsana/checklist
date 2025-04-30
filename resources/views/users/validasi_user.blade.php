@extends('layouts.vertical', ['title' => 'Validasi User'])

@section('css')
<style>
    /* Styling untuk modal vanilla */
    #hs-scale-animation-modal {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 100;
        justify-content: center;
        align-items: center;
    }

    #hs-scale-animation-modal.active {
        display: flex;
    }

    #hs-scale-animation-modal .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    #hs-scale-animation-modal .modal-content .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
    }

    #hs-scale-animation-modal .modal-content label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
    }

    #hs-scale-animation-modal .modal-content input {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    #hs-scale-animation-modal .modal-content .buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    #hs-scale-animation-modal .modal-content button {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    #hs-scale-animation-modal .modal-content .btn-close {
        background-color: #f1f1f1;
        color: #333;
    }

    #hs-scale-animation-modal .modal-content .btn-save {
        background-color: #4b7bec;
        color: white;
    }

    #hs-scale-animation-modal .modal-content .btn-close:hover {
        background-color: #ddd;
    }

    #hs-scale-animation-modal .modal-content .btn-save:hover {
        background-color: #3867d6;
    }

    /* Pastikan body bisa di-scroll saat modal ditutup */
    body.modal-open {
        overflow: hidden;
    }
</style>
@endsection

@section('content')
@include("layouts.shared/page-title", ["subtitle" => "User", "title" => "Validasi User"])

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Validasi User</h1>
        <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah User</a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead>
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Nomor</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Nama Lengkap</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Nama Kos</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Harga</th>
                                <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @foreach ($users as $user)
                            <tr data-user-id="{{ $user->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $user->nama_lengkap }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $user->kos->nama_kos }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200 price-cell">{{ $user->formatted_harga_kos }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                    <form action="{{ route('users.approve', $user) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="py-1 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                            <i class="material-symbols-rounded text-2xl">check</i>
                                        </button>
                                    </form>
                                    <button type="button" data-user-id="{{ $user->id }}" data-user-name="{{ $user->nama_lengkap }}" data-current-price="{{ $user->harga_kos }}" class="ml-2 py-1 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none open-edit-modal">
                                        <i class="material-symbols-rounded text-2xl">close</i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Edit Harga Kos (Vanilla) -->
<div id="hs-scale-animation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-[100] hidden">
    <div class="modal-content bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
        <div class="flex justify-between items-center border-b border-gray-200 pb-3">
            <h3 id="hs-scale-animation-modal-label" class="font-bold text-gray-800">
                Edit Harga Kos untuk <span id="modalUserName"></span>
            </h3>
            <button type="button" class="close-btn text-gray-500 hover:text-gray-700" onclick="document.getElementById('hs-scale-animation-modal').classList.add('hidden'); document.body.classList.remove('modal-open'); document.body.style.overflow = 'auto';">&times;</button>
        </div>
        <div class="p-4">
            <form id="editPriceForm" action="" method="POST" class="p-4 md:p-5">
                @csrf
                @method('POST')
                <input type="hidden" name="user_id" id="editUserId">
                <div class="grid gap-4 mb-4">
                    <div>
                        <label for="edit_harga_kos" class="block mb-2 text-sm font-medium text-gray-900">Harga Kos Baru</label>
                        <input type="number" name="harga_kos" id="edit_harga_kos" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required min="1">
                        @error('harga_kos')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 buttons">
                    <button type="button" onclick="document.getElementById('hs-scale-animation-modal').classList.add('hidden'); document.body.classList.remove('modal-open'); document.body.style.overflow = 'auto';" class="btn-close py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50">
                        Close
                    </button>
                    <button type="submit" class="btn-save py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Buka modal edit harga saat tombol "x" diklik
        document.querySelectorAll('.open-edit-modal').forEach(button => {
            button.addEventListener('click', function () {
                console.log('Button clicked for user ID:', this.getAttribute('data-user-id'));
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                const currentPrice = this.getAttribute('data-current-price');

                // Set data ke modal
                document.getElementById('modalUserName').textContent = userName;
                document.getElementById('editUserId').value = userId;
                document.getElementById('edit_harga_kos').value = currentPrice;

                // Set action form ke route update price
                const updatePriceUrl = "{{ route('users.updatePrice', ':userId') }}".replace(':userId', userId);
                console.log('Update Price URL:', updatePriceUrl);
                document.getElementById('editPriceForm').action = updatePriceUrl;

                // Buka modal
                const modal = document.getElementById('hs-scale-animation-modal');
                modal.classList.add('active');
                document.body.classList.add('modal-open');
                document.body.style.overflow = 'hidden'; // Mencegah scroll saat modal terbuka
            });
        });

        // Handle submit form edit harga
        document.getElementById('editPriceForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const hargaKosInput = document.getElementById('edit_harga_kos');
            const rawValue = hargaKosInput.value;

            if (!rawValue || rawValue <= 0) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Harga Kos harus diisi dan lebih dari 0.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            console.log('Submitting Raw Value:', rawValue);
            console.log('Form Action URL:', this.action);

            // Submit form menggunakan fetch
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                console.log('Response Status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response Data:', data);
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Harga kos berhasil diperbarui.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Tutup modal
                        const modal = document.getElementById('hs-scale-animation-modal');
                        modal.classList.remove('active');
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = 'auto';

                        // Perbarui harga di tabel tanpa refresh
                        const userId = document.getElementById('editUserId').value;
                        const row = document.querySelector(`tr[data-user-id="${userId}"]`);
                        if (row) {
                            const priceCell = row.querySelector('.price-cell');
                            if (priceCell) {
                                const formattedPrice = 'Rp. ' + parseInt(rawValue).toLocaleString('id-ID');
                                priceCell.textContent = formattedPrice;
                                console.log('Updated price in table:', formattedPrice);
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Terjadi kesalahan.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menyimpan data: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });

        // Handle tombol approve (centang)
        document.querySelectorAll('form[action*="approve"]').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'User akan divalidasi dan statusnya berubah menjadi aktif!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, validasi!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(this.action, {
                            method: 'POST',
                            body: new FormData(this),
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'User berhasil divalidasi.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    this.closest('tr').remove(); // Hapus baris dari tabel
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message || 'Terjadi kesalahan.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat memvalidasi.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                    }
                });
            });
        });
    });
</script>
@endsection