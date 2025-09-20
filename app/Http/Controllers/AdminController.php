<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\User;
use App\Models\Guideline;
use App\Models\Pembayaran;
use App\Models\Archive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Load semua data yang dibutuhkan untuk semua section
        $submissions = Submission::with(['user', 'pembayaran'])->orderBy('created_at', 'desc')->get();
        $users = User::where('role', '!=', 'admin')->withCount('submissions')->latest()->get();
        $guidelines = Guideline::latest()->get();
        $payments = Pembayaran::with(['submission.user'])->orderBy('created_at', 'desc')->get();
        
        // Untuk archive - buat dummy data jika model belum ada
        $archives = collect(); // Empty collection jika Archive model belum ada
        try {
            $archives = Archive::with(['user', 'submission', 'pembayaran'])->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            // Archive table mungkin belum ada
        }

        // Statistics
        $stats = [
            'totalSubmissions' => $submissions->count(),
            'pendingSubmissions' => $submissions->where('status', 'Menunggu Verifikasi')->count(),
            'approvedSubmissions' => $submissions->where('status', 'Diterima')->count(),
            'rejectedSubmissions' => $submissions->where('status', 'Ditolak')->count(),
            'pendingPayments' => $payments->where('status', 'Menunggu Pembayaran')->count(),
            'paidPayments' => $payments->where('status', 'Dibayar')->count(),
            'totalUsers' => $users->count(),
            'totalArchives' => $archives->count(),
        ];

        return view('admin.admin_dashboard', compact(
            'submissions', 'users', 'guidelines', 'payments', 'archives', 'stats'
        ));
    }

    public function updateStatus(Request $request, Submission $submission)
    {
        $request->validate(['status' => 'required|string']);
        
        $submission->status = $request->status;
        $submission->save();

        if ($request->status === 'Diterima') {
            $submission->payment_status = 'Menunggu Pembayaran';
            $submission->save();
            
            Pembayaran::firstOrCreate(
                ['submission_id' => $submission->id],
                ['status' => 'Menunggu Pembayaran', 'uploaded_by' => auth()->id(), 'uploaded_at' => now()]
            );
        }

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui!']);
    }

    public function updatePaymentStatus(Request $request, Submission $submission)
    {
        $request->validate(['payment_status' => 'required|string']);
        
        $submission->payment_status = $request->payment_status;

        if ($submission->pembayaran) {
            $submission->pembayaran->update([
                'status' => $request->payment_status,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);
        }

        if ($request->payment_status === 'Sudah Dibayar') {
            $submission->status = 'Berhasil';
        }

        $submission->save();
        return response()->json(['success' => true, 'message' => 'Status pembayaran berhasil diperbarui!']);
    }

    // Method untuk guidelines
    public function storeGuideline(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'requirements' => 'nullable|string',
        ]);

        $validatedData['example_data'] = null;
        $validatedData['requirements'] = $validatedData['requirements'] ? explode("\n", $validatedData['requirements']) : null;

        Guideline::create($validatedData);
        return response()->json(['success' => true, 'message' => 'Panduan berhasil ditambahkan!']);
    }

    public function updateGuideline(Request $request, Guideline $guideline)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'requirements' => 'nullable|string',
        ]);

        $validatedData['example_data'] = null;
        $validatedData['requirements'] = $validatedData['requirements'] ? explode("\n", $validatedData['requirements']) : null;

        $guideline->update($validatedData);
        return response()->json(['success' => true, 'message' => 'Panduan berhasil diperbarui!']);
    }

    public function destroyGuideline(Guideline $guideline)
    {
        $guideline->delete();
        return response()->json(['success' => true, 'message' => 'Panduan berhasil dihapus!']);
    }
}
