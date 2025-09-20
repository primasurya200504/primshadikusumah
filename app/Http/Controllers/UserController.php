<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Guideline;
use App\Models\SubmissionFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SubmissionRequest;
use App\Http\Requests\UpdateSubmissionRequest;

class UserController extends Controller
{
    /**
     * Menampilkan dashboard user dengan riwayat pengajuan dan panduan.
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();
            
            // Load relasi dengan eager loading untuk mencegah N+1 problem
            $submissions = $user->submissions()
                ->with(['files', 'pembayaran'])
                ->latest()
                ->take(10) // Limit untuk performance
                ->get();
            
            $guidelines = Guideline::select('id', 'title', 'content')
                ->orderBy('title')
                ->get();
            
            // Statistics untuk dashboard
            $stats = [
                'totalSubmissions' => $user->submissions()->count(),
                'pendingSubmissions' => $user->submissions()->where('status', 'Menunggu Verifikasi')->count(),
                'approvedSubmissions' => $user->submissions()->where('status', 'Diterima')->count(),
                'rejectedSubmissions' => $user->submissions()->where('status', 'Ditolak')->count(),
            ];

            return view('user.dashboard', compact('user', 'submissions', 'guidelines', 'stats'));
            
        } catch (\Exception $e) {
            Log::error('Error loading user dashboard', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('welcome')
                ->with('error', 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.');
        }
    }

    /**
     * Menyimpan pengajuan surat/data dari user.
     */
    public function submitForm(SubmissionRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $validatedData = $request->validated();
            
            // Generate nomor pengajuan unik
            $submissionNumber = $this->generateSubmissionNumber();
            
            // Buat submission baru
            $submission = Auth::user()->submissions()->create([
                'submission_number' => $submissionNumber,
                'data_type' => $validatedData['jenis_data'],
                'category' => $validatedData['kategori'],
                'start_date' => $validatedData['tanggal_mulai'] ?? null,
                'end_date' => $validatedData['tanggal_selesai'] ?? null,
                'purpose' => $validatedData['keperluan'],
                'status' => 'Menunggu Verifikasi',
                'payment_status' => 'Belum Dibayar',
            ]);

            // Handle upload files
            if ($request->hasFile('files')) {
                $this->handleFileUploads($request->file('files'), $submission, $validatedData['kategori']);
            }

            DB::commit();
            
            Log::info("Pengajuan berhasil dibuat", [
                'user_id' => Auth::id(),
                'submission_id' => $submission->id,
                'submission_number' => $submissionNumber
            ]);

            return redirect()->route('user.dashboard')
                ->with('success', "Pengajuan berhasil dikirim dengan nomor {$submissionNumber}. Admin akan memverifikasi pengajuan Anda.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Pengajuan gagal untuk user " . Auth::id(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->except(['files'])
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat pengajuan. Mohon coba lagi.'])
                ->withInput();
        }
    }

    /**
     * Update pengajuan yang ditolak (untuk re-submission)
     */
    public function updateSubmission(UpdateSubmissionRequest $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $submission = Submission::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('status', 'Ditolak')
                ->firstOrFail();

            $validatedData = $request->validated();

            // Update data submission
            $submission->update([
                'data_type' => $validatedData['jenis_data'],
                'start_date' => $validatedData['tanggal_mulai'] ?? null,
                'end_date' => $validatedData['tanggal_selesai'] ?? null,
                'purpose' => $validatedData['keperluan'],
                'status' => 'Menunggu Verifikasi',
                'rejection_note' => null,
            ]);

            // Handle file upload jika ada
            if ($request->hasFile('surat_pengantar')) {
                $this->handleSingleFileUpload($request->file('surat_pengantar'), $submission);
            }

            DB::commit();
            
            Log::info("Pengajuan berhasil diupdate", [
                'user_id' => Auth::id(),
                'submission_id' => $submission->id
            ]);

            return redirect()->route('user.dashboard')
                ->with('success', 'Pengajuan berhasil diperbarui. Admin akan memverifikasi kembali pengajuan Anda.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Update pengajuan gagal", [
                'user_id' => Auth::id(),
                'submission_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui pengajuan.'])
                ->withInput();
        }
    }

    /**
     * Menampilkan panduan pengajuan surat/data
     */
    public function showGuidelines()
    {
        try {
            $guidelines = Guideline::select('id', 'title', 'content', 'requirements')
                ->orderBy('title')
                ->get();
                
            return view('user.guidelines', compact('guidelines'));
            
        } catch (\Exception $e) {
            Log::error('Error loading guidelines', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('user.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat panduan.');
        }
    }

    /**
     * Menampilkan detail pengajuan
     */
    public function showSubmission($id)
    {
        try {
            $submission = Submission::with(['files', 'pembayaran'])
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return view('user.submission-detail', compact('submission'));
            
        } catch (\Exception $e) {
            Log::error('Error loading submission detail', [
                'user_id' => Auth::id(),
                'submission_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('user.dashboard')
                ->with('error', 'Pengajuan tidak ditemukan.');
        }
    }

    // PRIVATE HELPER METHODS
    
    /**
     * Generate nomor pengajuan unik
     */
    private function generateSubmissionNumber(): string
    {
        return 'BMKG/S.' . time() . '/' . date('Y');
    }

    /**
     * Handle multiple file uploads
     */
    private function handleFileUploads(array $files, Submission $submission, string $category): void
    {
        $folder = $category === 'PNBP' 
            ? 'submission_files/pnbp' 
            : 'submission_files/non-pnbp';

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $path = $file->store($folder, 'public');

            SubmissionFile::create([
                'submission_id' => $submission->id,
                'file_path' => $path,
                'file_name' => $originalName,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        // Update cover_letter_path untuk backward compatibility
        if (!empty($files)) {
            $submission->update([
                'cover_letter_path' => $files[0]->store($folder, 'public')
            ]);
        }
    }

    /**
     * Handle single file upload
     */
    private function handleSingleFileUpload($file, Submission $submission): void
    {
        $folder = $submission->category === 'PNBP'
            ? 'submission_files/pnbp'
            : 'submission_files/non-pnbp';

        $path = $file->store($folder, 'public');

        // Update atau create file record
        SubmissionFile::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]
        );

        $submission->update(['cover_letter_path' => $path]);
    }
}
