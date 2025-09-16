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
     */
    public function dashboard()
    {
        $user = Auth::user();
        $submissions = $user->submissions()->latest()->get();
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
            'jenis_data' => 'required',
            'kategori' => 'required|string|in:PNBP,Non-PNBP',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keperluan' => 'required',
        ];

        // Validasi file sesuai kategori
        if ($request->input('kategori') === 'PNBP') {
            $rules['files'] = 'required|array|min:1|max:1';
        } else { // Non-PNBP
            $rules['files'] = 'required|array|min:1|max:4';
        }
        $rules['files.*'] = 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'; // 10MB per file

        $validatedData = $request->validate($rules);

        try {
            $submissionNumber = 'BMKG/S.' . time() . '/' . date('Y');

            $submission = Auth::user()->submissions()->create([
                'submission_number' => $submissionNumber,
                'data_type' => $validatedData['jenis_data'],
                'category' => $validatedData['kategori'],
                'start_date' => $validatedData['tanggal_mulai'],
                'end_date' => $validatedData['tanggal_selesai'],
                'purpose' => $validatedData['keperluan'],
                'status' => 'Menunggu Verifikasi',
                'payment_status' => 'Belum Dibayar',
            ]);

            // Penentuan folder storage
            $folder = $validatedData['kategori'] === 'PNBP' ? 'submission_files/pnbp' : 'submission_files/nonpnbp';

            // Ambil file dari request
            $files = $request->file('files');
            if ($files && !is_array($files)) {
                $files = [$files];
            }

            if ($files) {
                foreach ($files as $file) {
                    $path = $file->store($folder, 'public');
                    SubmissionFile::create([
                        'submission_id' => $submission->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
                // Simpan path file pertama sebagai cover_letter_path (untuk link riwayat surat pengantar)
                $submission->cover_letter_path = $files[0]->store($folder, 'public');
                $submission->save();
            }

            return redirect()->route('user.dashboard')->with('success', 'Pengajuan berhasil dikirim.');
        } catch (\Exception $e) {
            Log::error("Submission failed: " . $e->getMessage());
            return redirect()->back()->withErrors(['gagal' => 'Terjadi kesalahan saat pengajuan. Mohon coba lagi.'])->withInput();
        }
    }
}
