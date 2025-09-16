<?php

namespace App\Http\Controllers;

use App\Models\Guideline;
use Illuminate\Http\Request;

class GuidelineController extends Controller
{
    public function index()
    {
        $guidelines = Guideline::all();
        $submissions = \App\Models\Submission::latest()->get();
        $users = \App\Models\User::where('role', 'user')->get();
        return view('admin.admin_dashboard', compact('guidelines', 'submissions', 'users'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'example_data' => 'nullable|string',
            'requirements' => 'nullable|string',
        ]);

        $validatedData['example_data'] = $validatedData['example_data'] ? json_decode($validatedData['example_data'], true) : null;
        $validatedData['requirements'] = $validatedData['requirements'] ? explode("\n", $validatedData['requirements']) : null;

        Guideline::create($validatedData);

        return back()->with('success', 'Panduan berhasil ditambahkan.');
    }

    public function update(Request $request, Guideline $guideline)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'example_data' => 'nullable|string',
            'requirements' => 'nullable|string',
        ]);

        $validatedData['example_data'] = $validatedData['example_data'] ? json_decode($validatedData['example_data'], true) : null;
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
