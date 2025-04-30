<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                return redirect('/dashboard')->with('error', 'Akses ditolak');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }


    public function validasi_user()
    {
        $users = User::where('status', 'pending')->with('kos')->get();
        return view('users.validasi_user', compact('users'));
    }

    // public function approve(User $user)
    // {
    //     $user->update(['status' => 'active']);
    //     return redirect()->route('users.validate')->with('success', 'User berhasil divalidasi dan diaktifkan.');
    // }

    public function approve(User $user)
    {
        $user->update(['status' => 'active']);
        return response()->json([
            'success' => true,
            'message' => 'User berhasil divalidasi.'
        ]);
    }

    public function updatePrice(Request $request, User $user)
    {
        $request->validate([
            'harga_kos' => 'required|numeric|min:1',
        ]);

        $user->update(['harga_kos' => $request->harga_kos]);
        return response()->json(['success' => true, 'message' => 'Harga kos berhasil diperbarui.']);
    }


    public function create()
    {
        return view('users.tambah_user');
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,user',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'level' => 'nullable|in:regular,tahfidz',
            'nama_lengkap' => 'required',
            'email' => 'required|email|unique:users',
            'no_hp' => 'required',
            // Tambahkan validasi lainnya
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'level' => $request->level,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            // Tambahkan field lainnya
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function show(User $user)
    {
        // if (!Gate::allows('is-admin')) {
        //     abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        // }

        return view('users.show', compact('user'));
    }

    public function resetPassword(User $user)
    {
        // if (!Gate::allows('is-admin')) {
        //     abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        // }

        $newPassword = 'password123'; // Password default atau generate random password
        $user->update(['password' => Hash::make($newPassword)]);

        return redirect()->route('users.show', $user)->with('success', 'Password user berhasil direset ke: ' . $newPassword);
    }

    public function destroy(User $user)
    {
        // Pastikan admin tidak bisa menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Hapus data terkait (misalnya payments, user_targets, dll.)
        $user->payments()->delete();
        $user->userTargets()->delete();

        // Hapus user
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function showPresensiForm()
    {
        $kegiatans = Kegiatan::where('tanggal', '>=', now())->get();
        return view('user.presensi', compact('kegiatans'));
    }

    public function storePresensi(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'kode_masuk' => 'required|numeric',
        ]);

        $kegiatan = Kegiatan::find($request->kegiatan_id);
        if ($kegiatan->kode_unik != $request->kode_masuk) {
            return redirect()->back()->withErrors(['kode_masuk' => 'Kode yang dimasukkan salah.']);
        }

        Presensi::updateOrCreate(
            ['user_id' => auth()->id(), 'kegiatan_id' => $request->kegiatan_id],
            ['hadir' => true, 'kode_masuk' => $request->kode_masuk]
        );

        return redirect()->back()->with('success', 'Presensi berhasil dicatat.');
    }
}
