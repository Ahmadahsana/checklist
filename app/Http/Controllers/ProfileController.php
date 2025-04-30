<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'nullable|string|max:255',
            'tempat_tanggal_lahir' => 'required|string|max:255',
            'alamat_asal' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'prodi' => 'required|string|max:255',
            'fakultas' => 'required|string|max:255',
            'angkatan' => 'required|string|max:20',
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'no_hp_ortu' => 'required|string|max:20',
            'no_hp_saudara_wali' => 'nullable|string|max:20',
            'nama_kos' => 'nullable|string|max:255',
            'harga_kos' => 'nullable|numeric',
            'foto' => 'nullable|image|max:2048', // Maks 2MB
        ]);

        // Handle upload foto
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('profiles', 'public');
            $user->foto = $fotoPath;
        }

        $user->update([
            'nama_lengkap' => $request->nama_lengkap,
            'nama_panggilan' => $request->nama_panggilan,
            'tempat_tanggal_lahir' => $request->tempat_tanggal_lahir,
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
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Password berhasil diperbarui');
    }
}
