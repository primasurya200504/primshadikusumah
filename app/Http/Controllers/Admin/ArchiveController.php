<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::with(['user'])
            ->where('is_archived', true)
            ->orderBy('archived_at', 'desc');

        // Filter berdasarkan bulan dan tahun
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('archived_at', $request->month)
                ->whereYear('archived_at', $request->year);
        } elseif ($request->filled('year')) {
            $query->whereYear('archived_at', $request->year);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan jenis data
        if ($request->filled('data_type')) {
            $query->where('data_type', $request->data_type);
        }

        $archives = $query->paginate(20);

        // Data untuk statistik
        $stats = $this->getArchiveStats($request);

        return view('admin.archive.index', compact('archives', 'stats'));
    }

    public function show($id)
    {
        $archive = Submission::with(['user'])->where('is_archived', true)->findOrFail($id);

        return view('admin.archive.show', compact('archive'));
    }

    public function archive(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);

        $submission->update([
            'is_archived' => true,
            'archived_at' => now(),
            'admin_notes' => $request->admin_notes
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil diarsipkan.');
    }

    public function unarchive($id)
    {
        $submission = Submission::findOrFail($id);

        $submission->update([
            'is_archived' => false,
            'archived_at' => null
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil dibatalkan dari arsip.');
    }

    public function uploadFinalDocument(Request $request, $id)
    {
        $request->validate([
            'final_document' => 'required|file|mimes:pdf,doc,docx|max:10240'
        ]);

        $submission = Submission::findOrFail($id);

        if ($request->hasFile('final_document')) {
            // Hapus file lama jika ada
            if ($submission->final_document_path) {
                Storage::disk('public')->delete($submission->final_document_path);
            }

            $file = $request->file('final_document');
            $filename = 'final_documents/' . $submission->submission_number . '_final_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('', $filename, 'public');

            $submission->update([
                'final_document_path' => $path
            ]);
        }

        return redirect()->back()->with('success', 'Dokumen final berhasil diupload.');
    }

    public function downloadDocument($id, $type)
    {
        $submission = Submission::findOrFail($id);

        $filePath = null;
        $fileName = null;

        switch ($type) {
            case 'cover_letter':
                $filePath = $submission->cover_letter_path;
                $fileName = $submission->submission_number . '_surat_pengantar.pdf';
                break;
            case 'payment_proof':
                $filePath = $submission->payment_proof_path;
                $fileName = $submission->submission_number . '_bukti_bayar.pdf';
                break;
            case 'final_document':
                $filePath = $submission->final_document_path;
                $fileName = $submission->submission_number . '_dokumen_final.pdf';
                break;
            default:
                abort(404);
        }

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($filePath, $fileName);
    }

    public function exportData(Request $request)
    {
        $query = Submission::with(['user'])
            ->where('is_archived', true);

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('archived_at', $request->month)
                ->whereYear('archived_at', $request->year);
        } elseif ($request->filled('year')) {
            $query->whereYear('archived_at', $request->year);
        }

        $archives = $query->get();

        $csvData = "No,Nomor Pengajuan,Pemohon,Email,Jenis Data,Status,Tanggal Arsip,Catatan Admin\n";

        foreach ($archives as $index => $archive) {
            $csvData .= ($index + 1) . "," .
                $archive->submission_number . "," .
                $archive->user->name . "," .
                $archive->user->email . "," .
                $archive->data_type . "," .
                $archive->status . "," .
                $archive->archived_at->format('d-m-Y') . "," .
                ($archive->admin_notes ?? '') . "\n";
        }

        $fileName = 'arsip_pengajuan_' . date('Y-m-d') . '.csv';

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    private function getArchiveStats($request)
    {
        $baseQuery = Submission::where('is_archived', true);

        if ($request->filled('month') && $request->filled('year')) {
            $baseQuery->whereMonth('archived_at', $request->month)
                ->whereYear('archived_at', $request->year);
        } elseif ($request->filled('year')) {
            $baseQuery->whereYear('archived_at', $request->year);
        }

        return [
            'total_archived' => $baseQuery->count(),
            'completed' => $baseQuery->where('status', 'Diterima')->count(),
            'rejected' => $baseQuery->where('status', 'Ditolak')->count(),
            'monthly_data' => $this->getMonthlyArchiveData($request->year ?? date('Y'))
        ];
    }

    private function getMonthlyArchiveData($year)
    {
        return Submission::where('is_archived', true)
            ->whereYear('archived_at', $year)
            ->select(
                DB::raw('MONTH(archived_at) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "Diterima" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN status = "Ditolak" THEN 1 ELSE 0 END) as rejected')
            )
            ->groupBy(DB::raw('MONTH(archived_at)'))
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();
    }
}
