<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Pembayaran;
use App\Models\Submission;

class AdminPembayaranController extends Controller
{
    /**
     * Display a listing of payments - DIPERBAIKI untuk menampilkan semua pembayaran
     */
    public function index()
    {
        // PERBAIKAN: Ambil semua pembayaran, tidak hanya yang submission-nya "Diterima"
        $pembayaran = Pembayaran::with(['submission.user', 'uploadedBy', 'verifiedBy'])
            ->latest()
            ->get();

        // Debug log
        Log::info('Admin Pembayaran Index - Total payments: ' . $pembayaran->count());

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    /**
     * Show the form for uploading billing.
     */
    public function formUpload($surat_id)
    {
        $submission = Submission::findOrFail($surat_id);

        if ($submission->status !== 'Diterima') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Pengajuan ini belum dapat diproses pembayaran.');
        }

        return view('admin.pembayaran.upload', compact('surat_id', 'submission'));
    }

    /**
     * Store billing file and data.
     */
    public function uploadBilling(Request $request, $surat_id)
    {
        $request->validate([
            'billing_file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'jumlah_tagihan' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'tanggal_billing' => 'required|date'
        ], [
            'billing_file.required' => 'File billing wajib diupload.',
            'billing_file.mimes' => 'Format file tidak didukung.',
            'billing_file.max' => 'Ukuran file maksimal 5MB.',
            'jumlah_tagihan.numeric' => 'Jumlah tagihan harus berupa angka.',
            'tanggal_billing.required' => 'Tanggal billing wajib diisi.',
        ]);

        try {
            $submission = Submission::findOrFail($surat_id);

            $file = $request->file('billing_file');
            $filename = time() . '_billing_' . $surat_id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/billing', $filename, 'public');

            $pembayaran = Pembayaran::updateOrCreate(
                ['submission_id' => $surat_id],
                [
                    'billing_file_path' => $path,
                    'billing_filename' => $filename,
                    'billing_amount' => $request->jumlah_tagihan,
                    'billing_note' => $request->keterangan,
                    'billing_date' => $request->tanggal_billing,
                    'status' => 'Menunggu Pembayaran',
                    'uploaded_by' => auth()->id(),
                    'uploaded_at' => now()
                ]
            );

            $submission->update([
                'payment_status' => 'Menunggu Pembayaran',
                'updated_at' => now()
            ]);

            Log::info("Billing uploaded for submission: {$submission->submission_number}");

            return redirect()->route('admin.dashboard')
                ->with('success', 'Billing berhasil diupload untuk pengajuan ' . $submission->submission_number . '. User akan dapat melihat dan mengunduh billing di dashboard mereka.');
        } catch (\Exception $e) {
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Log::error("Billing upload failed: " . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengupload billing: ' . $e->getMessage());
        }
    }

    /**
     * Download billing file untuk user
     */
    public function downloadBillingUser($id)
    {
        $pembayaran = Pembayaran::with('submission')
            ->where('id', $id)
            ->whereHas('submission', function ($q) {
                $q->where('user_id', auth()->id());
            })->firstOrFail();

        if (!$pembayaran->billing_file_path || !Storage::disk('public')->exists($pembayaran->billing_file_path)) {
            return redirect()->back()->with('error', 'File billing tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $pembayaran->billing_file_path,
            $pembayaran->billing_filename ?? 'billing.pdf'
        );
    }

    /**
     * Show upload form untuk user
     */
    public function showUploadFormUser($submission_id)
    {
        $submission = Submission::with('pembayaran')
            ->where('id', $submission_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!$submission->pembayaran || !$submission->pembayaran->billing_file_path) {
            return redirect()->back()->with('error', 'Billing belum tersedia.');
        }

        return view('user.pembayaran.upload', compact('submission'));
    }

    /**
     * Upload payment proof dari user - DIPERBAIKI dengan logging
     */
    public function uploadPaymentProofUser(Request $request, $submission_id)
    {
        $request->validate([
            'payment_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'payment_date' => 'required|date',
            'payment_note' => 'nullable|string|max:500'
        ], [
            'payment_proof.required' => 'Bukti pembayaran wajib diupload.',
            'payment_proof.mimes' => 'Format file tidak didukung.',
            'payment_proof.max' => 'Ukuran file maksimal 5MB.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.'
        ]);

        try {
            $submission = Submission::with('pembayaran')
                ->where('id', $submission_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            if (!$submission->pembayaran) {
                return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
            }

            $file = $request->file('payment_proof');
            $filename = time() . '_proof_' . $submission_id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/payment_proofs', $filename, 'public');

            // PERBAIKAN: Update status menjadi 'Dibayar' agar muncul di admin
            $submission->pembayaran->update([
                'payment_proof_path' => $path,
                'payment_proof_filename' => $filename,
                'payment_date' => $request->payment_date,
                'payment_note' => $request->payment_note,
                'status' => 'Dibayar' // Status ini yang akan ditampilkan di admin
            ]);

            $submission->update(['payment_status' => 'Menunggu Verifikasi']);

            Log::info("Payment proof uploaded for submission: {$submission->submission_number} by user: " . auth()->id());

            return redirect()->route('user.dashboard')
                ->with('success', 'Bukti pembayaran berhasil diupload! Admin akan memverifikasi pembayaran Anda.');
        } catch (\Exception $e) {
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Log::error("Payment proof upload failed: " . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show billing details.
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with(['submission.user', 'uploadedBy', 'verifiedBy'])->findOrFail($id);

        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    /**
     * Download billing file (untuk admin).
     */
    public function downloadBilling($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        if (!$pembayaran->billing_file_path || !Storage::disk('public')->exists($pembayaran->billing_file_path)) {
            return redirect()->back()->with('error', 'File billing tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $pembayaran->billing_file_path,
            $pembayaran->billing_filename ?? 'billing.pdf'
        );
    }

    /**
     * Update payment status - DIPERBAIKI dengan logging yang lebih baik
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Menunggu Pembayaran,Dibayar,Terverifikasi,Selesai,Ditolak',
            'keterangan_status' => 'nullable|string|max:500'
        ]);

        $pembayaran = Pembayaran::with('submission')->findOrFail($id);

        $oldStatus = $pembayaran->status;

        $pembayaran->update([
            'status' => $request->status,
            'verified_by' => auth()->id(),
            'verified_at' => now()
        ]);

        $submissionStatus = $this->mapToSubmissionPaymentStatus($request->status);
        $pembayaran->submission->update(['payment_status' => $submissionStatus]);

        Log::info("Payment status updated from '{$oldStatus}' to '{$request->status}' for pembayaran ID: {$id}");

        return redirect()->back()->with('success', 'Status pembayaran berhasil diupdate.');
    }

    /**
     * Helper method untuk mapping status - DIPERBAIKI
     */
    private function mapToSubmissionPaymentStatus($paymentStatus)
    {
        $mapping = [
            'Menunggu Pembayaran' => 'Menunggu Pembayaran',
            'Dibayar' => 'Menunggu Verifikasi',
            'Terverifikasi' => 'Sudah Dibayar',
            'Selesai' => 'Selesai',
            'Ditolak' => 'Ditolak'
        ];

        return $mapping[$paymentStatus] ?? 'Menunggu Pembayaran';
    }

    /**
     * TAMBAHAN: Method untuk debugging data pembayaran
     */
    public function debugPayments()
    {
        $allPayments = Pembayaran::with(['submission.user'])->get();

        $debugData = [];
        foreach ($allPayments as $payment) {
            $debugData[] = [
                'id' => $payment->id,
                'submission_number' => $payment->submission ? $payment->submission->submission_number : 'No submission',
                'user_name' => $payment->submission && $payment->submission->user ? $payment->submission->user->name : 'No user',
                'status' => $payment->status,
                'has_billing' => $payment->billing_file_path ? 'Yes' : 'No',
                'has_proof' => $payment->payment_proof_path ? 'Yes' : 'No',
                'created_at' => $payment->created_at
            ];
        }

        return response()->json([
            'total_payments' => count($debugData),
            'payments' => $debugData
        ]);
    }

    /**
     * TAMBAHAN: Method untuk memfilter pembayaran berdasarkan status
     */
    public function getPaymentsByStatus($status = null)
    {
        $query = Pembayaran::with(['submission.user']);

        if ($status) {
            $query->where('status', $status);
        }

        $pembayaran = $query->latest()->get();

        Log::info("Retrieved payments with status: {$status}, count: " . $pembayaran->count());

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    /**
     * Delete billing data.
     */
    public function destroy($id)
    {
        try {
            $pembayaran = Pembayaran::with('submission')->findOrFail($id);

            if ($pembayaran->billing_file_path && Storage::disk('public')->exists($pembayaran->billing_file_path)) {
                Storage::disk('public')->delete($pembayaran->billing_file_path);
            }

            if ($pembayaran->payment_proof_path && Storage::disk('public')->exists($pembayaran->payment_proof_path)) {
                Storage::disk('public')->delete($pembayaran->payment_proof_path);
            }

            $pembayaran->submission->update(['payment_status' => null]);
            $pembayaran->delete();

            Log::info("Payment deleted: {$id}");

            return redirect()->route('admin.pembayaran.index')
                ->with('success', 'Data pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error("Failed to delete payment: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}

