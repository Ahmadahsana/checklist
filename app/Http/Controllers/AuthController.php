<?php

namespace App\Http\Controllers;

use App\Models\ContentLandingPage;
use App\Models\Kos;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($request->username === 'rumahprestasimahasiswa' && $request->password === '12345678') {
            return redirect('/daftar-anggota-baru/rumahprestasimahasiswa');
        } elseif ($request->username === 'rumahtahfidzmahasiswa' && $request->password === '12345678') {
            return redirect('/daftar-anggota-baru/rumahtahfidzmahasiswa');
        }

        if (Auth::attempt($credentials)) {
            // Tentukan redirect berdasarkan role
            if (auth()->user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } else {
                return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function landing()
    {
        $contentLandingPage = ContentLandingPage::first();
        return view('landing', compact('contentLandingPage'));
    }

    public function daftar_baru($level)
    {
        // return $level;
        $kos = Kos::all();
        return view('daftar_baru', compact(['level', 'kos']));
    }

    public function daftar_baru_store(Request $request)
    {

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'username' => 'required|unique:users',
            'password' => 'required|min:8',
            'level' => 'nullable|in:regular,tahfidz',
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'nullable|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat_asal' => 'nullable|string|max:255',
            'no_hp' => 'required|string|max:19',
            'email' => 'required|email|unique:users|max:255',
            'prodi' => 'nullable|string|max:255',
            'fakultas' => 'nullable|string|max:255',
            'angkatan' => 'nullable|integer|min:1900|max:' . date('Y'),
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'no_hp_ortu' => 'nullable|string|max:19',
            // 'no_hp_saudara_wali' => 'nullable|string|max:15',
            'nama_kos' => 'nullable|string|max:255',
            'harga_kos' => 'nullable|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Hanya validasi, penyimpanan foto dikosongkan dulu
        ]);

        // Jika validasi gagal, kembali ke form dengan pesan error
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }



        try {

            $user_baru = User::create([
                'username' => $request->no_hp, // Menggunakan no_hp sebagai username
                'password' => Hash::make($request->password),
                'role' => 'user', // Default role sebagai 'user'
                'level' => $request->level,
                'nama_lengkap' => $request->nama_lengkap,
                'nama_panggilan' => $request->nama_panggilan,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat_asal' => $request->alamat_asal,
                'no_hp' => $request->no_hp,
                'email' => $request->email,
                'prodi' => $request->prodi,
                'fakultas' => $request->fakultas,
                'angkatan' => $request->angkatan,
                'nama_ayah' => $request->nama_ayah,
                'nama_ibu' => $request->nama_ibu,
                'no_hp_ortu' => $request->no_hp_ortu,
                'no_hp_saudara_wali' => $request->no_hp_saudara_wali,
                'nama_kos' => $request->nama_kos,
                'harga_kos' => $request->harga_kos,
                'foto' => null, // Kosongkan untuk sementara
                'status' => 'pending', // Kosongkan untuk sementara
            ]);

            Payment::create([
                'user_id' => $user_baru->id,
                'harga_kos' => $request->harga_kos,
                'max_bayar' => 3,
                'total_terbayar' => 0,
                'kurang' => $request->harga_kos,
                'status' => 'belum',
                'batas_waktu_angsuran' => now()->addDays(30), // Set batas waktu angsuran 30 hari dari sekarang
            ]);

            // Redirect ke halaman login dengan pesan sukses
            return redirect('/login')->with('success', 'User berhasil didaftarkan. Silakan login.');
        } catch (\Exception $e) {
            // Jika ada error, kembali ke form dengan pesan error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mendaftarkan user. Silakan coba lagi.')->withInput();
        }

        // User::create([
        //     'username' => $request->no_hp,
        //     'password' => Hash::make($request->password),
        //     'role' => 'user',
        //     'level' => $request->level,
        //     'nama_lengkap' => $request->nama_lengkap,
        //     'nama_panggilan' => $request->nama_panggilan,
        //     'tempat_tanggal_lahir' => $request->tempat_tanggal_lahir,
        //     'alamat_asal' => $request->alamat_asal,
        //     'no_hp' => $request->no_hp,
        //     'email' => $request->email,
        //     'prodi' => $request->prodi,
        //     'fakultas' => $request->fakultas,
        //     'angkatan' => $request->angkatan,
        //     'nama_ayah' => $request->nama_ayah,
        //     'nama_ibu' => $request->nama_ibu,
        //     'no_hp_ortu' => $request->no_hp_ortu,
        //     'no_hp_saudara_wali' => $request->no_hp_saudara_wali,
        //     'nama_kos' => $request->nama_kos,
        //     'harga_kos' => $request->harga_kos,
        //     'foto' => null, // Kosongkan untuk sementara
        // ]);

        // // return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');

        // return redirect('/login')->with('success', 'User berhasil ditambahkan');
    }
}
