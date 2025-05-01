<?php

namespace App\Http\Controllers;

use App\Models\ContentLandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentLandingPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $landingpage = ContentLandingPage::first();
        return view('admin.landingpage', compact('landingpage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // dd('atas');

        $contentLandingPage = ContentLandingPage::first();
        if (!$contentLandingPage) {
            // Buat baru jika tidak ada
            $contentLandingPage = new ContentLandingPage();
        }

        // dd('lewat');
        $contentLandingPage->title = $request->input('title');
        $contentLandingPage->content = $request->input('content');

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($contentLandingPage->image && Storage::disk('public')->exists($contentLandingPage->image)) {
                Storage::disk('public')->delete($contentLandingPage->image);
            }

            // Simpan gambar baru
            $filename = $request->file('image')->store('landingpage', 'public');
            $contentLandingPage->image = $filename;
        }

        $contentLandingPage->save();

        // dd('bawah');

        return redirect()->route('admin.landingpage')->with('success', 'Content Landing Page updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ContentLandingPage $contentLandingPage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContentLandingPage $contentLandingPage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContentLandingPage $contentLandingPage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContentLandingPage $contentLandingPage)
    {
        //
    }
}
