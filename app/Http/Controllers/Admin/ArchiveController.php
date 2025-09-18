<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Submission;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class ArchiveController extends Controller
{
    // Untuk dashboard view (bukan API)
    public function index()
    {
        $archives = Archive::with(['user', 'submission.pembayaran'])
            ->latest('archived_at')
            ->paginate(15);

        $stats = [
            'total' => Archive::count(),
            'completed' => Archive::where('status', 'Diterima')->count(),
            'rejected' => Archive::where('status', 'Ditolak')->count(),
            'monthly' => Archive::whereMonth('archived_at', now()->month)
                ->whereYear('archived_at', now()->year)
                ->count()
        ];

        // Jika request AJAX, return JSON
        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'archives' => $archives->items(),
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $archives->currentPage(),
                    'last_page' => $archives->lastPage(),
                    'total' => $archives->total()
                ]
            ]);
        }

        // Jika normal request, return view (untuk dashboard)
        return view('admin.archive.index', compact('archives', 'stats'));
    }

    // Method untuk archive submission
    public function archive(Request $request, $submissionId)
    {
        try {
            $submission = Submission::with('user')->findOrFail($submissionId);

            // Check if already archived
            if (Archive::where('submission_id', $submissionId)->exists()) {
                return response()->json(['error' => 'Pengajuan sudah diarsipkan'], 400);
            }

            $archive = Archive::create([
                'submission_id' => $submission->id,
                'user_id' => $submission->user_id,
                'submission_number' => $submission->submission_number ?? 'SUB-' . $submission->id,
                'data_type' => $submission->data_type ?? 'Tidak diketahui',
                'status' => $submission->status,
                'admin_notes' => $request->admin_notes,
                'cover_letter_path' => $submission->cover_letter_path,
                'final_document_path' => $submission->final_document_path ?? null,
                'archived_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil diarsipkan',
                'archive' => $archive
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengarsipkan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $archive = Archive::with(['user', 'submission', 'submission.pembayaran'])->findOrFail($id);

        if (request()->ajax()) {
            return response()->json($archive);
        }

        return view('admin.archive.detail', compact('archive'));
    }

    // Method untuk unarchive
    public function unarchive($id)
    {
        try {
            $archive = Archive::findOrFail($id);
            $archive->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dikeluarkan dari arsip'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengeluarkan dari arsip: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadDocument($id, $type)
    {
        $archive = Archive::with('submission.pembayaran')->findOrFail($id);

        $filePath = match ($type) {
            'cover_letter' => $archive->cover_letter_path,
            'payment_proof' => $archive->submission->pembayaran?->payment_proof_path,
            'billing' => $archive->submission->pembayaran?->billing_file_path,
            'final_document' => $archive->final_document_path,
            default => null
        };

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $filename = basename($filePath);
        return Storage::disk('public')->download($filePath, $filename);
    }

    public function exportData(Request $request)
    {
        $query = Archive::with(['user', 'submission.pembayaran']);

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

        // Generate CSV with UTF-8 BOM for Excel compatibility
        $csvData = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csvData .= "Nomor Pengajuan,Pemohon,Email,Jenis Data,Status,Tanggal Arsip,Catatan Admin\n";

        foreach ($archives as $archive) {
            $csvData .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $archive->submission_number,
                $archive->user->name,
                $archive->user->email,
                $archive->data_type,
                $archive->status,
                $archive->archived_at->format('d/m/Y H:i'),
                str_replace('"', '""', $archive->admin_notes ?? '')
            );
        }

        $filename = 'arsip_data_' . now()->format('Y_m_d_His') . '.csv';

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
