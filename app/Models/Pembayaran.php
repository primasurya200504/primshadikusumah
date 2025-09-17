<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\User;
use App\Models\Guideline;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $submissions = Submission::with('user')->latest()->get();
        $users = User::where('role', '!=', 'admin')->latest()->get();
        $guidelines = Guideline::latest()->get();

        $totalSubmissions = Submission::count();
        $pendingSubmissions = Submission::where('status', 'Menunggu Verifikasi')->count();
        $pendingPayments = Submission::where('payment_status', 'Menunggu Pembayaran')->count();

        return view('admin.admin_dashboard', compact(
            'submissions',
            'users',
            'guidelines',
            'totalSubmissions',
            'pendingSubmissions',
            'pendingPayments'
        ));
    }

    public function updateStatus(Request $request, Submission $submission)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $submission->status = $request->status;
        $submission->save();

        // Jika status diubah menjadi "Diterima", update payment_status
        if ($request->status === 'Diterima') {
            $submission->payment_status = 'Menunggu Pembayaran';
            $submission->save();
        }

        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui!');
    }

    public function updatePaymentStatus(Request $request, Submission $submission)
    {
        $request->validate([
            'payment_status' => 'required|string'
        ]);

        $submission->payment_status = $request->payment_status;

        // Jika payment_status diubah menjadi "Sudah Dibayar", ubah status menjadi "Berhasil"
        if ($request->payment_status === 'Sudah Dibayar') {
            $submission->status = 'Berhasil';
        }

        $submission->save();

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui!');
    }
}
