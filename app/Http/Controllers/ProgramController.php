<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
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
        $programs = Program::all();
        return view('programs.index', compact('programs'));
    }

    public function create()
    {
        return view('programs.create');
    }

    public function store(Request $request)
    {
        // if (!Gate::allows('is-admin')) {
        //     abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        // }

        $request->validate([
            'nama_program' => 'required|string|max:255',
            'target' => 'required|string|max:255',
            'level' => 'required|in:regular,tahfidz,both',
            'type' => 'required|in:numeric,boolean',
            'unit' => 'required|string|max:50', // Validasi untuk satuan
        ]);

        Program::create($request->all());

        return redirect()->route('programs.index')->with('success', 'Program berhasil ditambahkan');
    }

    public function edit(Program $program)
    {
        // if (!Gate::allows('is-admin')) {
        //     abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        // }

        return view('programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {


        $request->validate([
            'nama_program' => 'required|string|max:255',
            'target' => 'required|string|max:255',
            'level' => 'required|in:regular,tahfidz,both',
            'type' => 'required|in:numeric,boolean',
            'unit' => 'required|string|max:50',
        ]);

        $program->update($request->all());

        return redirect()->route('programs.index')->with('success', 'Program berhasil diperbarui');
    }
}
