<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Guideline;
use App\Models\SubmissionFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Menampilkan dashboard user dengan riwayat pengajuan dan panduan.
     * PERBAIKAN: Menambahkan relasi 'pembayaran' untuk menampilkan status pembayaran
     */
    public function dashboard()
    {
        $user = Auth::user();

        // PERBAIKAN: Load relasi 'files' dan 'pembayaran' untuk menampilkan status pembayaran
        $submissions = $user->submissions()
            ->with(['files', 'pembayaran'])
            ->latest()
            ->get();

        $guidelines = Guideline::all();

        return view('user.dashboard', compact('user', 'submissions', 'guidelines'));
    }

    /**
     * Menyimpan pengajuan surat/data dari user.
     */
    public function submitForm(Request $request)
    {
        // Validasi umum
        $rules = [
            'jenis_data' => 'required|string|max:255',
            'kategori' => 'required|string|in:PNBP,Non-PNBP',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keperluan' => 'required|string|max:1000',
        ];

        // Validasi file sesuai kategori
        if ($request->input('kategori') === 'PNBP') {
            $rules['files'] = 'required|array|min:1|max:1';
        } else { // Non-PNBP
            $rules['files'] = 'required|array|min:1|max:4';
        }
        $rules['files.*'] = 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'; // 10MB per file

        $validatedData = $request->validate($rules, [
            'jenis_data.required' => 'Jenis data wajib dipilih.',
            'kategori.required' => 'Kategori pengajuan wajib dipilih.',
            'keperluan.required' => 'Keperluan penggunaan data wajib diisi.',
            'files.required' => 'File surat pengantar wajib diupload.',
            'files.*.mimes' => 'Format file tidak didukung. Gunakan PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
            'files.*.max' => 'Ukuran file maksimal 10MB.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        try {
            // Generate nomor pengajuan unik
            $submissionNumber = 'BMKG/S.' . time() . '/' . date('Y');

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

            // Penentuan folder storage berdasarkan kategori
            $folder = $validatedData['kategori'] === 'PNBP'
                ? 'submission_files/pnbp'
                : 'submission_files/non-pnbp';

            // Handle upload files
            $files = $request->file('files');
            if ($files && !is_array($files)) {
                $files = [$files];
            }

            if ($files) {
                foreach ($files as $index => $file) {
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

                // Simpan path file pertama sebagai cover_letter_path untuk backward compatibility
                $submission->update([
                    'cover_letter_path' => $files[0]->store($folder, 'public')
                ]);
            }

            Log::info("Pengajuan berhasil dibuat", [
                'user_id' => Auth::id(),
                'submission_id' => $submission->id,
                'submission_number' => $submissionNumber
            ]);

            return redirect()->route('user.dashboard')
                ->with('success', 'Pengajuan berhasil dikirim dengan nomor ' . $submissionNumber . '. Admin akan memverifikasi pengajuan Anda.');
        } catch (\Exception $e) {
            Log::error("Pengajuan gagal untuk user " . Auth::id(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['gagal' => 'Terjadi kesalahan saat pengajuan. Mohon coba lagi.'])
                ->withInput();
        }
    }

    /**
     * Menampilkan panduan pengajuan surat/data
     */
    public function showGuidelines()
    {
        $guidelines = Guideline::orderBy('title')->get();

        return view('user.guidelines', compact('guidelines'));
    }

    /**
     * Update pengajuan yang ditolak (untuk re-submission)
     */
    public function updateSubmission(Request $request, $id)
    {
        $submission = Submission::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'Ditolak')
            ->firstOrFail();

        $rules = [
            'jenis_data' => 'required|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keperluan' => 'required|string|max:1000',
            'surat_pengantar' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ];

        $validatedData = $request->validate($rules);

        try {
            // Update data submission
            $submission->update([
                'data_type' => $validatedData['jenis_data'],
                'start_date' => $validatedData['tanggal_mulai'] ?? null,
                'end_date' => $validatedData['tanggal_selesai'] ?? null,
                'purpose' => $validatedData['keperluan'],
                'status' => 'Menunggu Verifikasi',
                'rejection_note' => null, // Clear rejection note
            ]);

            // Handle file upload jika ada
            if ($request->hasFile('surat_pengantar')) {
                $file = $request->file('surat_pengantar');
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

            Log::info("Pengajuan berhasil diupdate", [
                'user_id' => Auth::id(),
                'submission_id' => $submission->id
            ]);

            return redirect()->route('user.dashboard')
                ->with('success', 'Pengajuan berhasil diperbarui. Admin akan memverifikasi kembali pengajuan Anda.');
        } catch (\Exception $e) {
            Log::error("Update pengajuan gagal", [
                'user_id' => Auth::id(),
                'submission_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withErrors(['gagal' => 'Terjadi kesalahan saat memperbarui pengajuan.'])
                ->withInput();
        }
    }

    /**
     * Menampilkan detail pengajuan
     */
    public function showSubmission($id)
    {
        $submission = Submission::with(['files', 'pembayaran'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('user.submission-detail', compact('submission'));
    }
}
