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
        $submissions = Submission::with('user')->latest()->get(); // Tambahkan relasi user
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
        $submission = Submission::with('user')->findOrFail($submissionId);
        return view('admin.show_request', compact('submission'));
    }

    public function updateStatus(Request $request, Submission $submission)
    {
        $request->validate(['status' => 'required|string']);

        $submission->status = $request->status;

        // Update payment_status berdasarkan status yang dipilih
        if ($request->status === 'Diterima') {
            $submission->payment_status = 'Menunggu Upload Billing';
        } elseif ($request->status === 'Ditolak') {
            $submission->payment_status = 'Belum Dibayar';
            // Bisa menambahkan catatan penolakan jika diperlukan
            if ($request->has('rejection_note')) {
                $submission->rejection_note = $request->rejection_note;
            }
        } elseif ($request->status === 'Diproses') {
            // Status tetap seperti sebelumnya atau sesuai kebutuhan
            if ($submission->payment_status === 'Belum Dibayar') {
                $submission->payment_status = 'Belum Dibayar';
            }
        }

        $submission->save();

        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui!');
    }

    public function updatePaymentStatus(Request $request, Submission $submission)
    {
        $request->validate(['payment_status' => 'required|string']);

        $submission->payment_status = $request->input('payment_status');

        // Jika pembayaran sudah dibayar, ubah status submission menjadi Berhasil
        if ($request->input('payment_status') === 'Sudah Dibayar') {
            $submission->status = 'Berhasil';
        }

        $submission->save();

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui!');
    }
}
