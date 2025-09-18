<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\User;
use App\Models\Guideline;
use App\Models\Pembayaran;
use App\Models\Archive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

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
        $paidPayments = Pembayaran::where('status', 'Dibayar')->count();

        // Archive statistics - tambahkan ini
        $archivedSubmissions = Archive::count();
        $completedArchives = Archive::where('status', 'Diterima')->count();
        $rejectedArchives = Archive::where('status', 'Ditolak')->count();
        $monthlyArchives = Archive::whereMonth('archived_at', now()->month)
            ->whereYear('archived_at', now()->year)
            ->count();

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
            'paidPayments',
            'archivedSubmissions',
            'completedArchives',
            'rejectedArchives',
            'monthlyArchives'
        ));
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

    private function mapPaymentStatus($paymentStatus)
    {
        $mapping = [
            'Menunggu Pembayaran' => 'Menunggu Pembayaran',
            'Sudah Dibayar' => 'Terverifikasi',
            'Belum Dibayar' => 'Menunggu Pembayaran',
            'Menunggu Verifikasi' => 'Dibayar',
            'Ditolak' => 'Ditolak',
            'Selesai' => 'Selesai'
        ];

        return $mapping[$paymentStatus] ?? 'Menunggu Pembayaran';
    }

    // Archive Management Methods
    public function getArchiveData(Request $request)
    {
        $query = Archive::with(['user', 'pembayaran']);

        // Apply filters
        if ($request->year) {
            $query->whereYear('archived_at', $request->year);
        }
        if ($request->month) {
            $query->whereMonth('archived_at', $request->month);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->data_type) {
            $query->where('data_type', $request->data_type);
        }

        $archives = $query->orderBy('archived_at', 'desc')->get();

        $stats = [
            'total' => Archive::count(),
            'completed' => Archive::where('status', 'Diterima')->count(),
            'rejected' => Archive::where('status', 'Ditolak')->count(),
            'monthly' => Archive::whereMonth('archived_at', now()->month)
                ->whereYear('archived_at', now()->year)
                ->count()
        ];

        return response()->json([
            'archives' => $archives,
            'stats' => $stats
        ]);
    }

    public function archiveSubmission(Request $request, $submissionId)
    {
        $submission = Submission::findOrFail($submissionId);

        // Check if already archived
        if (Archive::where('submission_id', $submissionId)->exists()) {
            return response()->json(['error' => 'Pengajuan sudah diarsipkan'], 400);
        }

        $archive = Archive::create([
            'submission_id' => $submission->id,
            'user_id' => $submission->user_id,
            'submission_number' => $submission->submission_number,
            'data_type' => $submission->data_type,
            'status' => $submission->status,
            'admin_notes' => $request->admin_notes,
            'cover_letter_path' => $submission->cover_letter_path,
            'final_document_path' => $submission->final_document_path ?? null,
            'archived_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Pengajuan berhasil diarsipkan']);
    }

    public function showArchiveDetail($id)
    {
        $archive = Archive::with(['user', 'submission', 'pembayaran'])->findOrFail($id);
        return view('admin.archive.detail', compact('archive'));
    }

    public function unarchiveSubmission($id)
    {
        $archive = Archive::findOrFail($id);
        $archive->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dikeluarkan dari arsip']);
    }

    public function downloadArchiveDocument($id, $type)
    {
        $archive = Archive::findOrFail($id);

        $filePath = match ($type) {
            'cover_letter' => $archive->cover_letter_path,
            'payment_proof' => $archive->pembayaran?->payment_proof_path,
            'billing' => $archive->pembayaran?->billing_file_path,
            'final_document' => $archive->final_document_path,
            default => null
        };

        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::download($filePath);
    }

    public function exportArchiveData(Request $request)
    {
        $query = Archive::with(['user', 'pembayaran']);

        // Apply filters
        if ($request->year) {
            $query->whereYear('archived_at', $request->year);
        }
        if ($request->month) {
            $query->whereMonth('archived_at', $request->month);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->data_type) {
            $query->where('data_type', $request->data_type);
        }

        $archives = $query->get();

        // Generate CSV
        $csvData = "Nomor Pengajuan,Pemohon,Email,Jenis Data,Status,Tanggal Arsip,Catatan Admin\n";

        foreach ($archives as $archive) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $archive->submission_number,
                $archive->user->name,
                $archive->user->email,
                $archive->data_type,
                $archive->status,
                $archive->archived_at->format('Y-m-d'),
                str_replace([',', "\n", "\r"], [';', ' ', ' '], $archive->admin_notes ?? '')
            );
        }

        $filename = 'arsip_data_' . now()->format('Y_m_d_His') . '.csv';

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
