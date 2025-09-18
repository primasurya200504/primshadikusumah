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
    public function index()
    {
        $archives = Archive::with(['user', 'pembayaran'])
            ->orderBy('archived_at', 'desc')
            ->get();

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

    public function store(Request $request, $submissionId)
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

    public function show($id)
    {
        $archive = Archive::with(['user', 'submission', 'pembayaran'])->findOrFail($id);
        return view('admin.archive.detail', compact('archive'));
    }

    public function destroy($id)
    {
        $archive = Archive::findOrFail($id);
        $archive->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dikeluarkan dari arsip']);
    }

    public function download($id, $type)
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

    public function exportData(Request $request)
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
