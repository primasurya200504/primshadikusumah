<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\User;
use App\Models\Guideline;
use App\Models\Pembayaran;
use App\Models\Archive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\GuidelineRequest;
use App\Http\Requests\UpdateGuidelineRequest;

class AdminController extends Controller
{
    /**
     * Admin dashboard dengan caching untuk performance
     */
    public function dashboard()
    {
        try {
            // Cache data untuk 5 menit
            $data = Cache::remember('admin_dashboard_data', 300, function () {
                return $this->getDashboardData();
            });

            return view('admin.admin_dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Error loading admin dashboard', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('welcome')
                ->with('error', 'Terjadi kesalahan saat memuat dashboard admin.');
        }
    }

    /**
     * Update status pengajuan dengan validasi
     */
    public function updateStatus(Request $request, Submission $submission)
    {
        $request->validate([
            'status' => 'required|string|in:Menunggu Verifikasi,Diterima,Ditolak,Berhasil',
            'rejection_note' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $oldStatus = $submission->status;

            $submission->update([
                'status' => $request->status,
                'rejection_note' => $request->rejection_note,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);

            // Jika status berubah menjadi Diterima
            if ($request->status === 'Diterima') {
                $this->handleApprovedSubmission($submission);
            }

            // Jika status berubah menjadi Ditolak
            if ($request->status === 'Ditolak') {
                $this->handleRejectedSubmission($submission);
            }

            DB::commit();

            // Clear cache
            Cache::forget('admin_dashboard_data');

            Log::info('Status pengajuan berhasil diperbarui', [
                'submission_id' => $submission->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui!',
                'new_status' => $request->status
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating submission status', [
                'submission_id' => $submission->id,
                'status' => $request->status,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status.'
            ], 500);
        }
    }

    /**
     * Update status pembayaran dengan validasi
     */
    public function updatePaymentStatus(Request $request, Submission $submission)
    {
        $request->validate([
            'payment_status' => 'required|string|in:Belum Dibayar,Menunggu Pembayaran,Sudah Dibayar,Refund'
        ]);

        DB::beginTransaction();

        try {
            $oldPaymentStatus = $submission->payment_status;

            $submission->update([
                'payment_status' => $request->payment_status
            ]);

            // Update pembayaran record jika ada
            if ($submission->pembayaran) {
                $submission->pembayaran->update([
                    'status' => $request->payment_status,
                    'verified_by' => auth()->id(),
                    'verified_at' => now()
                ]);
            }

            // Jika pembayaran sudah dibayar, ubah status menjadi Berhasil
            if ($request->payment_status === 'Sudah Dibayar') {
                $submission->update(['status' => 'Berhasil']);
            }

            DB::commit();

            // Clear cache
            Cache::forget('admin_dashboard_data');

            Log::info('Status pembayaran berhasil diperbarui', [
                'submission_id' => $submission->id,
                'old_payment_status' => $oldPaymentStatus,
                'new_payment_status' => $request->payment_status,
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diperbarui!',
                'new_payment_status' => $request->payment_status
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating payment status', [
                'submission_id' => $submission->id,
                'payment_status' => $request->payment_status,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status pembayaran.'
            ], 500);
        }
    }

    /**
     * Store guideline dengan validasi
     */
    public function storeGuideline(GuidelineRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $guideline = Guideline::create([
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
                'requirements' => $validatedData['requirements'] ?
                    explode("\n", $validatedData['requirements']) : null,
                'example_data' => null,
                'created_by' => auth()->id()
            ]);

            // Clear cache
            Cache::forget('admin_dashboard_data');

            Log::info('Panduan berhasil ditambahkan', [
                'guideline_id' => $guideline->id,
                'title' => $validatedData['title'],
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Panduan berhasil ditambahkan!',
                'guideline' => $guideline
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating guideline', [
                'data' => $request->validated(),
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan panduan.'
            ], 500);
        }
    }

    /**
     * Update guideline dengan validasi
     */
    public function updateGuideline(UpdateGuidelineRequest $request, Guideline $guideline)
    {
        try {
            $validatedData = $request->validated();

            $guideline->update([
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
                'requirements' => $validatedData['requirements'] ?
                    explode("\n", $validatedData['requirements']) : null,
                'updated_by' => auth()->id()
            ]);

            // Clear cache
            Cache::forget('admin_dashboard_data');

            Log::info('Panduan berhasil diperbarui', [
                'guideline_id' => $guideline->id,
                'title' => $validatedData['title'],
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Panduan berhasil diperbarui!',
                'guideline' => $guideline
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating guideline', [
                'guideline_id' => $guideline->id,
                'data' => $request->validated(),
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui panduan.'
            ], 500);
        }
    }

    /**
     * Delete guideline dengan soft delete
     */
    public function destroyGuideline(Guideline $guideline)
    {
        try {
            $guideline->delete();

            // Clear cache
            Cache::forget('admin_dashboard_data');

            Log::info('Panduan berhasil dihapus', [
                'guideline_id' => $guideline->id,
                'title' => $guideline->title,
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Panduan berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting guideline', [
                'guideline_id' => $guideline->id,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus panduan.'
            ], 500);
        }
    }

    // PRIVATE HELPER METHODS

    /**
     * Get dashboard data dengan optimasi query
     */
    private function getDashboardData(): array
    {
        // Load data dengan eager loading
        $submissions = Submission::with(['user:id,name,email', 'pembayaran:id,submission_id,status'])
            ->select('id', 'user_id', 'submission_number', 'data_type', 'status', 'payment_status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::where('role', '!=', 'admin')
            ->withCount('submissions')
            ->select('id', 'name', 'email', 'created_at')
            ->latest()
            ->get();

        $guidelines = Guideline::select('id', 'title', 'content', 'created_at')
            ->latest()
            ->get();

        $payments = Pembayaran::with(['submission.user:id,name'])
            ->select('id', 'submission_id', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        // Archive dengan try-catch
        $archives = collect();
        try {
            $archives = Archive::with(['user:id,name', 'submission:id,submission_number'])
                ->select('id', 'user_id', 'submission_id', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::warning('Archive model tidak ditemukan', ['error' => $e->getMessage()]);
        }

        // Calculate statistics
        $stats = [
            'totalSubmissions' => $submissions->count(),
            'pendingSubmissions' => $submissions->where('status', 'Menunggu Verifikasi')->count(),
            'approvedSubmissions' => $submissions->where('status', 'Diterima')->count(),
            'rejectedSubmissions' => $submissions->where('status', 'Ditolak')->count(),
            'pendingPayments' => $payments->where('status', 'Menunggu Pembayaran')->count(),
            'paidPayments' => $payments->where('status', 'Dibayar')->count(),
            'totalUsers' => $users->count(),
            'totalArchives' => $archives->count(),
        ];

        return compact('submissions', 'users', 'guidelines', 'payments', 'archives', 'stats');
    }

    /**
     * Handle approved submission
     */
    private function handleApprovedSubmission(Submission $submission): void
    {
        $submission->update(['payment_status' => 'Menunggu Pembayaran']);

        Pembayaran::firstOrCreate(
            ['submission_id' => $submission->id],
            [
                'status' => 'Menunggu Pembayaran',
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now()
            ]
        );
    }

    /**
     * Handle rejected submission
     */
    private function handleRejectedSubmission(Submission $submission): void
    {
        $submission->update(['payment_status' => 'Belum Dibayar']);

        // Optional: Send notification to user
        // Notification::send($submission->user, new SubmissionRejected($submission));
    }
}
