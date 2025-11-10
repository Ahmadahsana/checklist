<?php

use App\Http\Controllers\AdminFeeController;
use App\Http\Controllers\AdminProgressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentLandingPageController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPaymentController;
use App\Http\Controllers\UserTargetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// require __DIR__ . '/auth.php';

Route::get('/', [AuthController::class, 'landing'])->name('landing');


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/daftar-anggota-baru/{level}', [AuthController::class, 'daftar_baru']);
Route::post('/daftar_baru', [AuthController::class, 'daftar_baru_store']);

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show'); // Route untuk detail user
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset.password');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy'); // Route untuk hapus

    Route::get('admin/dashboard', [TargetController::class, 'admin_dashboard'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserTargetController::class, 'dashboard'])->name('user.dashboard');
    Route::post('/dashboard/update', [UserTargetController::class, 'updateDashboardChart'])->name('dashboard.update');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('/programs/create', [ProgramController::class, 'create'])->name('programs.create');
    Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
    Route::get('/programs/{program}/edit', [ProgramController::class, 'edit'])->name('programs.edit');
    Route::put('/programs/{program}', [ProgramController::class, 'update'])->name('programs.update');
    Route::delete('/programs/{program}/hapus', [ProgramController::class, 'destroy'])->name('programs.destroy');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/targets', [UserTargetController::class, 'index'])->name('user-targets.index');
    Route::post('/targets', [UserTargetController::class, 'store'])->name('user-targets.store');
    Route::get('/targets/{programId}', [UserTargetController::class, 'show'])->name('user-targets.show');
    Route::get('/progress-personal', [UserTargetController::class, 'personalProgress'])->name('user-targets.personal-progress');
    Route::post('/progress-personal/update', [UserTargetController::class, 'updatePersonalProgress'])->name('user-targets.personal-progress.update');
});

Route::middleware(['auth', 'role:admin'])->group(function () { // Pastikan hanya admin yang bisa akses
    Route::get('/admin/progress/user', [AdminProgressController::class, 'showUserProgress'])->name('admin.progress.user'); // Halaman utama progres per user
    Route::post('/admin/progress/user/update', [AdminProgressController::class, 'userProgressUpdate'])->name('admin.progress.user.update');
    Route::get('/admin/progress/user/search', [AdminProgressController::class, 'searchUsers'])->name('admin.progress.user.search');

    Route::get('/admin/progress', [AdminProgressController::class, 'index'])->name('admin.progress.index');
    Route::post('/admin/progress/update', [AdminProgressController::class, 'update'])->name('admin.progress.update');
    Route::get('/admin/progress/overall', [AdminProgressController::class, 'overallProgress'])->name('admin.progress.overall');

    Route::get('/admin/rank', [AdminProgressController::class, 'rank'])->name('admin.rank');
});

Route::middleware('auth')->group(function () {
    // Administrasi Biaya oleh Admin
    Route::get('/admin/manage-fees', [AdminFeeController::class, 'index'])->name('admin.manage-fees');
    Route::post('/admin/manage-fees/update', [AdminFeeController::class, 'updateFee'])->name('admin.manage-fees.update');
    Route::post('/admin/manage-fees/set-due-date', [AdminFeeController::class, 'setDueDate'])->name('admin.manage-fees.set-due-date');
    Route::get('/admin/manage-fees/{userId}', [AdminFeeController::class, 'show'])->name('admin.manage-fees.show');
    Route::get('/admin/konfirmasi/{paymentId}', [AdminFeeController::class, 'halaman_konfirmasi'])->name('admin.konfirmasi');
    Route::put('/admin/validasi-pembayaran/{id}', [AdminFeeController::class, 'validasiPembayaran'])->name('admin.validasi-pembayaran');
    Route::put('/admin/tolak-pembayaran/{id}', [AdminFeeController::class, 'tolakPembayaran'])->name('admin.tolak-pembayaran');

    // Rekapan Pembayaran
    Route::get('/admin/payment-summary', [AdminFeeController::class, 'summary'])->name('admin.payment-summary');
    Route::get('/admin/komfirmasi_pembayaran', [AdminFeeController::class, 'konfirmasi_pembayaran'])->name('admin.konfirmasi_pembayaran');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    // Pembayaran oleh User
    Route::get('/user/payment', [UserPaymentController::class, 'index'])->name('user.payment');
    Route::post('/user/payment/submit', [UserPaymentController::class, 'submitPayment'])->name('user.payment.submit');
});

// Route::group(['prefix' => '/'], function () {
//     Route::get('{any}', [RoutingController::class, 'root'])->name('any');
// });

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Pembayaran oleh User
    Route::get('/kos', [KosController::class, 'index'])->name('kos.index');
    Route::get('/admin/kos/create', [KosController::class, 'create'])->name('kos.create');
    Route::post('/admin/kos', [KosController::class, 'store'])->name('kos.store');
    Route::get('/admin/kos/{kos}', [KosController::class, 'show'])->name('kos.show');
    Route::put('/admin/kos/{kos}', [KosController::class, 'update'])->name('kos.update'); // Route untuk update
    Route::delete('/admin/kos/{kos}', [KosController::class, 'destroy'])->name('kos.destroy');

    // Validasi User disini
    Route::get('/admin/validasi_user', [UserController::class, 'validasi_user'])->name('users.validate');
    Route::post('/admin/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('/admin/users/{user}/update-price', [UserController::class, 'updatePrice'])->name('users.updatePrice');


    // landingpage
    Route::get('/admin/landingpage', [ContentLandingPageController::class, 'index'])->name('admin.landingpage');
    Route::post('/admin/landingpage', [ContentLandingPageController::class, 'store'])->name('admin.landingpage.update');
});

Route::get('/icon', function () {
    return view('icons');
});
Route::get('/awal', function () {
    return view('index');
});

// presensi

Route::get('/presensi-kegiatan', [PresensiController::class, 'index'])->name('presensi.index');
Route::get('/user/presensi', [UserController::class, 'showPresensiForm'])->name('user.presensi');
Route::post('/user/presensi', [UserController::class, 'storePresensi'])->name('user.presensi.store');

// Route untuk Pengelola (Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/presensi-kegiatan', [KegiatanController::class, 'index'])->name('presensi.index');
    Route::get('/presensi-kegiatan/create', [KegiatanController::class, 'create'])->name('presensi.create');
    Route::post('/presensi-kegiatan', [KegiatanController::class, 'store'])->name('presensi.store');
    Route::get('/presensi-kegiatan/{kegiatan}', [KegiatanController::class, 'show'])->name('presensi.show');
});

// Route untuk User Personal (Peserta)
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/presensi-personal', [PresensiController::class, 'index'])->name('presensi.personal');
    Route::post('/presensi-personal/submit', [PresensiController::class, 'submit'])->name('presensi.submit');
    Route::get('/presensi-riwayat', [PresensiController::class, 'riwayat'])->name('presensi.riwayat');
});
