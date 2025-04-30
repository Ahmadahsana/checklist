<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use Illuminate\Http\Request;

class KosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kos = Kos::all();
        return view('admin.kos.index', compact('kos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.kos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kos' => 'required|string|max:255',
        ]);

        Kos::create(['nama_kos' => $request->nama_kos]);

        return redirect()->route('kos.index')->with('success', 'Kos berhasil ditambahkan.');
    }

    public function show(Kos $kos)
    {
        return view('admin.kos.show', compact('kos'));
    }

    public function update(Request $request, Kos $kos)
    {
        $request->validate([
            'nama_kos' => 'required|string|max:255',
        ]);

        $kos->update(['nama_kos' => $request->nama_kos]);

        return redirect()->route('kos.index')->with('success', 'Kos berhasil diperbarui.');
    }

    public function destroy(Kos $kos)
    {
        $kos->delete();
        return redirect()->route('kos.index')->with('success', 'Kos berhasil dihapus.');
    }
}
