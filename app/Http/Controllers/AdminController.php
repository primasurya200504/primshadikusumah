<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Guideline;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function getDashboardData()
    {
        $totalSubmissions = Submission::count();
        $pendingSubmissions = Submission::where('status', 'Menunggu Verifikasi')->count();
        $pendingPayments = Submission::where('payment_status', 'Menunggu Pembayaran')->count();
        $submissions = Submission::latest()->get();
        $guidelines = Guideline::all();
        $users = User::where('role', 'user')->get();

        return compact('totalSubmissions', 'pendingSubmissions', 'pendingPayments', 'submissions', 'guidelines', 'users');
    }

    public function dashboard()
    {
        $data = $this->getDashboardData();
        return view('admin.admin_dashboard', $data);
    }

    public function showRequest($submissionId)
    {
        $submission = Submission::findOrFail($submissionId);
        return view('admin.show_request', compact('submission'));
    }

    // Metode yang diperbaiki dan dinamai ulang untuk konsistensi
    public function updateStatus(Request $request, Submission $submission)
    {
        $request->validate(['status' => 'required|string']);
        $submission->status = $request->status;
        $submission->save();

        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui!');
    }

    // Metode untuk memperbarui status pembayaran
    public function updatePaymentStatus(Request $request, Submission $submission)
    {
        $request->validate(['payment_status' => 'required|string']);
        $submission->payment_status = $request->input('payment_status');
        $submission->save();

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui!');
    }
}
