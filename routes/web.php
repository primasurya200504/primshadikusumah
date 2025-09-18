<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

// Import controller yang ada di project Anda
use App\Http\Controllers\GuidelineController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\AdminPembayaranController;
use App\Http\Controllers\AdminSuratController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home Routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes (Laravel UI)
Auth::routes();

// Home Route (redirect based on role)
Route::get('/home', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }
    return redirect()->route('login');
})->name('home');

// User Routes (Protected by auth middleware)
Route::middleware(['auth'])->group(function () {

    // User Dashboard
    Route::get('/user/dashboard', function () {
        $user = auth()->user();
        $submissions = \App\Models\Submission::with('pembayaran')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSubmissions = $submissions->count();
        $pendingSubmissions = $submissions->where('status', 'Menunggu Verifikasi')->count();
        $approvedSubmissions = $submissions->where('status', 'Diterima')->count();

        return view('user.dashboard', compact('submissions', 'totalSubmissions', 'pendingSubmissions', 'approvedSubmissions'));
    })->name('user.dashboard');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Submission Management
    Route::resource('submissions', \App\Http\Controllers\UserController::class);
    Route::get('/user/submission/create', [UserController::class, 'create'])->name('user.submission.create');
    Route::post('/user/submission', [UserController::class, 'store'])->name('user.submission.store');
    Route::get('/user/submission/{id}', [UserController::class, 'show'])->name('user.submission.show');
    Route::get('/user/submission/{id}/edit', [UserController::class, 'edit'])->name('user.submission.edit');
    Route::put('/user/submission/{id}', [UserController::class, 'update'])->name('user.submission.update');
    Route::delete('/user/submission/{id}', [UserController::class, 'destroy'])->name('user.submission.destroy');

    // Payment Routes
    Route::get('/payment/{submission}', [PembayaranController::class, 'show'])->name('payment.show');
    Route::post('/payment/{submission}/upload', [PembayaranController::class, 'uploadProof'])->name('payment.upload');
    Route::get('/payment/{submission}/download/{type}', [PembayaranController::class, 'downloadFile'])->name('payment.download');

    // Surat Management
    Route::get('/surat', [SuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/{id}/download', [SuratController::class, 'download'])->name('surat.download');

    // Guidelines Routes
    Route::get('/guidelines', [GuidelineController::class, 'index'])->name('guidelines.index');
    Route::get('/guidelines/{guideline}', [GuidelineController::class, 'show'])->name('guidelines.show');
});

// Admin Routes (Protected by auth middleware)
Route::middleware(['auth'])->group(function () {

    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Admin Submission Management
    Route::post('/admin/submission/{submission}/status', [AdminController::class, 'updateStatus'])->name('admin.submission.updateStatus');
    Route::post('/admin/submission/{submission}/payment-status', [AdminController::class, 'updatePaymentStatus'])->name('admin.submission.updatePaymentStatus');

    // Admin Pembayaran Management
    Route::get('/admin/pembayaran', [AdminPembayaranController::class, 'index'])->name('admin.pembayaran.index');
    Route::post('/admin/pembayaran/{id}/verify', [AdminPembayaranController::class, 'verify'])->name('admin.pembayaran.verify');
    Route::post('/admin/pembayaran/{id}/reject', [AdminPembayaranController::class, 'reject'])->name('admin.pembayaran.reject');
    Route::post('/admin/pembayaran/{id}/upload-billing', [AdminPembayaranController::class, 'uploadBilling'])->name('admin.pembayaran.uploadBilling');
    Route::get('/admin/pembayaran/{id}/download/{type}', [AdminPembayaranController::class, 'download'])->name('admin.pembayaran.download');

    // Admin Surat Management
    Route::get('/admin/surat', [AdminSuratController::class, 'index'])->name('admin.surat.index');
    Route::get('/admin/surat/{id}/download', [AdminSuratController::class, 'download'])->name('admin.surat.download');

    // Archive Management Routes
    Route::get('/admin/archive/api', [AdminController::class, 'getArchiveData'])->name('admin.archive.api');
    Route::post('/admin/archive/{id}', [AdminController::class, 'archiveSubmission'])->name('admin.archive.store');
    Route::get('/admin/archive/{id}/detail', [AdminController::class, 'showArchiveDetail'])->name('admin.archive.show');
    Route::delete('/admin/archive/{id}', [AdminController::class, 'unarchiveSubmission'])->name('admin.archive.destroy');
    Route::get('/admin/archive/{id}/download/{type}', [AdminController::class, 'downloadArchiveDocument'])->name('admin.archive.download');
    Route::get('/admin/archive/export', [AdminController::class, 'exportArchiveData'])->name('admin.archive.export');

    // Admin Guidelines Management
    Route::resource('/admin/guidelines', GuidelineController::class, ['as' => 'admin']);

    // Admin Users Management
    Route::get('/admin/users', [AdminController::class, 'manageUsers'])->name('admin.users');
});

// Public Routes (tanpa auth)
Route::get('/public/guidelines', [GuidelineController::class, 'publicIndex'])->name('public.guidelines');
Route::get('/public/guidelines/{guideline}', [GuidelineController::class, 'publicShow'])->name('public.guidelines.show');

// API Routes (for AJAX requests)
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/submissions', function () {
        return \App\Models\Submission::with('user', 'pembayaran')->get();
    })->name('api.submissions');

    Route::get('/payments', function () {
        return \App\Models\Pembayaran::with('submission.user')->get();
    })->name('api.payments');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
