<?php

namespace App\Http\Controllers;

use App\Models\Guideline;
use Illuminate\Http\Request;

class GuidelineController extends Controller
{
    public function index()
    {
        $guidelines = Guideline::latest()->get();
        return view('admin.guidelines.index', compact('guidelines'));
    }

    public function showUserGuidelines()
    {
        // PERBAIKAN: Hapus example_data untuk user
        $guidelines = Guideline::select(['title', 'content', 'requirements'])->latest()->get();
        return view('user.guidelines', compact('guidelines'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'requirements' => 'nullable|string',
        ]);

        $validatedData['example_data'] = null; // PERBAIKAN: Selalu set null
        $validatedData['requirements'] = $validatedData['requirements'] ? explode("\n", $validatedData['requirements']) : null;

        Guideline::create($validatedData);
        return back()->with('success', 'Panduan berhasil ditambahkan.');
    }

    public function update(Request $request, Guideline $guideline)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'requirements' => 'nullable|string',
        ]);

        $validatedData['example_data'] = null; // PERBAIKAN: Selalu set null
        $validatedData['requirements'] = $validatedData['requirements'] ? explode("\n", $validatedData['requirements']) : null;

        $guideline->update($validatedData);
        return back()->with('success', 'Panduan berhasil diperbarui.');
    }

    public function destroy(Guideline $guideline)
    {
        $guideline->delete();
        return back()->with('success', 'Panduan berhasil dihapus.');
    }
}
