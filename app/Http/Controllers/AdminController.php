<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\User;
use App\Models\Guideline;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Debug: Log untuk memeriksa data pembayaran
        $totalPembayaran = Pembayaran::count();
        $pembayaranDibayar = Pembayaran::where('status', 'Dibayar')->count();

        Log::info("Debug Admin Dashboard - Total Pembayaran: " . $totalPembayaran);
        Log::info("Debug Admin Dashboard - Pembayaran Dibayar: " . $pembayaranDibayar);

        // Load submissions dengan relasi pembayaran dan user
        $submissions = Submission::with(['user', 'pembayaran'])
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::where('role', '!=', 'admin')->latest()->get();
        $guidelines = Guideline::latest()->get();

        // Debug: Log submissions yang memiliki pembayaran
        foreach ($submissions as $submission) {
            if ($submission->pembayaran) {
                Log::info("Submission {$submission->submission_number} has payment with status: {$submission->pembayaran->status}");
            } else {
                Log::info("Submission {$submission->submission_number} has NO payment");
            }
        }

        // Statistik yang sudah ada
        $totalSubmissions = Submission::count();
        $pendingSubmissions = Submission::where('status', 'Menunggu Verifikasi')->count();

        // Update statistik pembayaran menggunakan model Pembayaran
        $pendingPayments = Pembayaran::where('status', 'Menunggu Pembayaran')->count();
        $paidPayments = Pembayaran::where('status', 'Dibayar')->count(); // Tambahan untuk yang sudah dibayar

        // Debug statistik
        Log::info("Debug Stats - Pending Payments: " . $pendingPayments);
        Log::info("Debug Stats - Paid Payments: " . $paidPayments);

        return view('admin.admin_dashboard', compact(
            'submissions',
            'users',
            'guidelines',
            'totalSubmissions',
            'pendingSubmissions',
            'pendingPayments',
            'paidPayments' // Tambahkan ini
        ));
    }

    /**
     * Tambahkan method untuk debugging pembayaran
     */
    public function debugPayments()
    {
        $pembayaran = Pembayaran::with('submission.user')->get();

        foreach ($pembayaran as $payment) {
            dump([
                'id' => $payment->id,
                'submission_id' => $payment->submission_id,
                'submission_number' => $payment->submission ? $payment->submission->submission_number : 'No submission',
                'user_name' => $payment->submission && $payment->submission->user ? $payment->submission->user->name : 'No user',
                'status' => $payment->status,
                'payment_proof' => $payment->payment_proof_path ? 'Has proof' : 'No proof',
                'billing_file' => $payment->billing_file_path ? 'Has billing' : 'No billing'
            ]);
        }

        return response('Debug completed - check output above');
    }

    public function updateStatus(Request $request, Submission $submission)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $submission->status = $request->status;
        $submission->save();

        // Jika status diubah menjadi "Diterima", buat record pembayaran dan update payment_status
        if ($request->status === 'Diterima') {
            $submission->payment_status = 'Menunggu Pembayaran';
            $submission->save();

            // Buat record pembayaran baru jika belum ada
            $pembayaran = Pembayaran::firstOrCreate(
                ['submission_id' => $submission->id],
                [
                    'status' => 'Menunggu Pembayaran',
                    'uploaded_by' => auth()->id(),
                    'uploaded_at' => now()
                ]
            );

            Log::info("Created/Updated Pembayaran for submission: " . $submission->id . " with ID: " . $pembayaran->id);
        }

        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui!');
    }

    public function updatePaymentStatus(Request $request, Submission $submission)
    {
        $request->validate([
            'payment_status' => 'required|string'
        ]);

        $submission->payment_status = $request->payment_status;

        // Update record pembayaran jika ada
        if ($submission->pembayaran) {
            // Map payment_status dari submission ke status pembayaran
            $pembayaranStatus = $this->mapPaymentStatus($request->payment_status);

            $submission->pembayaran->update([
                'status' => $pembayaranStatus,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            Log::info("Updated pembayaran status for submission: " . $submission->id . " to: " . $pembayaranStatus);
        } else {
            // Jika belum ada record pembayaran, buat baru
            $pembayaran = Pembayaran::create([
                'submission_id' => $submission->id,
                'status' => $this->mapPaymentStatus($request->payment_status),
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now()
            ]);

            Log::info("Created new pembayaran for submission: " . $submission->id . " with status: " . $pembayaran->status);
        }

        // Jika payment_status diubah menjadi "Sudah Dibayar", ubah status menjadi "Berhasil"
        if ($request->payment_status === 'Sudah Dibayar') {
            $submission->status = 'Berhasil';
        }

        $submission->save();

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui!');
    }

    /**
     * Map payment status dari submission ke status pembayaran
     */
    private function mapPaymentStatus($paymentStatus)
    {
        $mapping = [
            'Menunggu Pembayaran' => 'Menunggu Pembayaran',
            'Sudah Dibayar' => 'Terverifikasi',
            'Belum Dibayar' => 'Menunggu Pembayaran',
            'Menunggu Verifikasi' => 'Dibayar', // Tambahkan ini untuk status user upload bukti
            'Ditolak' => 'Ditolak',
            'Selesai' => 'Selesai'
        ];

        return $mapping[$paymentStatus] ?? 'Menunggu Pembayaran';
    }

    /**
     * Method untuk mendapatkan statistik pembayaran detail
     */
    public function getPaymentStats()
    {
        return [
            'total_billing' => Pembayaran::whereNotNull('billing_file_path')->count(),
            'menunggu_pembayaran' => Pembayaran::where('status', 'Menunggu Pembayaran')->count(),
            'sudah_dibayar' => Pembayaran::where('status', 'Dibayar')->count(),
            'terverifikasi' => Pembayaran::where('status', 'Terverifikasi')->count(),
            'selesai' => Pembayaran::where('status', 'Selesai')->count(),
        ];
    }

    /**
     * Method khusus untuk mengambil data pembayaran untuk tabel terpisah
     */
    public function getPembayaranData()
    {
        return Pembayaran::with(['submission.user'])
            ->whereHas('submission', function ($query) {
                $query->where('status', 'Diterima');
            })
            ->latest()
            ->get();
    }

    /**
     * Method untuk testing query pembayaran
     */
    public function testPembayaranQuery()
    {
        // Test 1: Ambil semua pembayaran
        $allPayments = Pembayaran::count();
        echo "Total payments: " . $allPayments . "<br>";

        // Test 2: Ambil pembayaran dengan status Dibayar
        $paidPayments = Pembayaran::where('status', 'Dibayar')->count();
        echo "Paid payments: " . $paidPayments . "<br>";

        // Test 3: Ambil pembayaran dengan bukti upload
        $withProof = Pembayaran::whereNotNull('payment_proof_path')->count();
        echo "Payments with proof: " . $withProof . "<br>";

        // Test 4: Ambil submissions dengan pembayaran
        $submissionsWithPayments = Submission::whereHas('pembayaran')->count();
        echo "Submissions with payments: " . $submissionsWithPayments . "<br>";

        // Test 5: Detail pembayaran terbaru
        $latestPayments = Pembayaran::with('submission.user')->latest()->take(5)->get();
        echo "<h3>Latest 5 payments:</h3>";
        foreach ($latestPayments as $payment) {
            echo "ID: " . $payment->id . " - ";
            echo "Status: " . $payment->status . " - ";
            echo "Submission: " . ($payment->submission ? $payment->submission->submission_number : 'No submission') . " - ";
            echo "User: " . ($payment->submission && $payment->submission->user ? $payment->submission->user->name : 'No user') . "<br>";
        }

        return response('Test completed');
    }
}
