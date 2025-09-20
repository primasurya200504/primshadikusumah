<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPembayaranController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuidelineController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\Admin\ArchiveController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard redirect route
Route::get('/dashboard', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    }
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// User Routes
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::post('/submit', [UserController::class, 'submitForm'])->name('submit');
    Route::get('/guidelines', [GuidelineController::class, 'showUserGuidelines'])->name('guidelines');
    Route::post('/upload-surat', [SuratController::class, 'uploadSurat'])->name('upload-surat');

    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/download/{id}', [AdminPembayaranController::class, 'downloadBillingUser'])->name('download');
        Route::get('/upload/{submission_id}', [AdminPembayaranController::class, 'showUploadFormUser'])->name('upload');
        Route::post('/upload/{submission_id}', [AdminPembayaranController::class, 'uploadPaymentProofUser'])->name('upload.store');
    });
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // PERBAIKAN: Tambahkan routes untuk management pages yang hilang
    Route::get('/manage-requests', [AdminController::class, 'manageRequests'])->name('manage.requests');
    Route::get('/manage-payments', [AdminController::class, 'managePayments'])->name('manage.payments');
    Route::get('/manage-users', [AdminController::class, 'manageUsers'])->name('manage.users');
    Route::get('/manage-archive', [AdminController::class, 'manageArchive'])->name('manage.archive');

    Route::patch('/submission/{submission}/update-status', [AdminController::class, 'updateStatus'])->name('submission.updateStatus');
    Route::patch('/submissions/{submission}/update-payment-status', [AdminController::class, 'updatePaymentStatus'])->name('submissions.updatePaymentStatus');

    // Guidelines management
    Route::get('/guidelines', [GuidelineController::class, 'index'])->name('guidelines.index');
    Route::post('/guidelines', [GuidelineController::class, 'store'])->name('guidelines.store');
    Route::patch('/guidelines/{guideline}', [GuidelineController::class, 'update'])->name('guidelines.update');
    Route::delete('/guidelines/{guideline}', [GuidelineController::class, 'destroy'])->name('guidelines.destroy');

    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/', [AdminPembayaranController::class, 'index'])->name('index');
        Route::get('/upload/{surat_id}', [AdminPembayaranController::class, 'formUpload'])->name('upload');
        Route::post('/upload/{surat_id}', [AdminPembayaranController::class, 'uploadBilling'])->name('upload.store');
        Route::get('/show/{id}', [AdminPembayaranController::class, 'show'])->name('show');
        Route::get('/download/{id}', [AdminPembayaranController::class, 'downloadBilling'])->name('download');
        Route::patch('/status/{id}', [AdminPembayaranController::class, 'updateStatus'])->name('status.update');
        Route::delete('/{id}', [AdminPembayaranController::class, 'destroy'])->name('destroy');
    });

    // Archive routes
    Route::prefix('archive')->name('archive.')->group(function () {
        Route::get('/', [AdminController::class, 'manageArchive'])->name('index');
        Route::get('/{id}', [AdminController::class, 'showArchiveDetail'])->name('show');
        Route::post('/{id}', [AdminController::class, 'archiveSubmission'])->name('store');
        Route::delete('/{id}', [AdminController::class, 'unarchiveSubmission'])->name('unarchive');
        Route::get('/{id}/download/{type}', [AdminController::class, 'downloadArchiveDocument'])->name('download');
        Route::get('/export/data', [AdminController::class, 'exportArchiveData'])->name('export');
    });
});

require __DIR__ . '/auth.php';
