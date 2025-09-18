<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPembayaranController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuidelineController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\PembayaranController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.dashboard');
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
    Route::get('/guidelines', [UserController::class, 'showGuidelines'])->name('guidelines');
    Route::post('/upload-surat', [SuratController::class, 'uploadSurat'])->name('upload-surat');

    // Routes pembayaran untuk user
    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/download/{id}', [AdminPembayaranController::class, 'downloadBillingUser'])->name('download');
        Route::get('/upload/{submission_id}', [AdminPembayaranController::class, 'showUploadFormUser'])->name('upload');
        Route::post('/upload/{submission_id}', [AdminPembayaranController::class, 'uploadPaymentProofUser'])->name('upload.store');
    });
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::patch('/submission/{submission}/update-status', [AdminController::class, 'updateStatus'])->name('submission.updateStatus');
    Route::patch('/submissions/{submission}/update-payment-status', [AdminController::class, 'updatePaymentStatus'])->name('submissions.updatePaymentStatus');

    Route::resource('guidelines', GuidelineController::class)->only(['index', 'store', 'update', 'destroy'])->names('guidelines');

    // Admin Pembayaran Routes
    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/', [AdminPembayaranController::class, 'index'])->name('index');
        Route::get('/upload/{surat_id}', [AdminPembayaranController::class, 'formUpload'])->name('upload');
        Route::post('/upload/{surat_id}', [AdminPembayaranController::class, 'uploadBilling'])->name('upload.store');
        Route::get('/show/{id}', [AdminPembayaranController::class, 'show'])->name('show');
        Route::get('/download/{id}', [AdminPembayaranController::class, 'downloadBilling'])->name('download');
        Route::patch('/status/{id}', [AdminPembayaranController::class, 'updateStatus'])->name('status.update');
        Route::delete('/{id}', [AdminPembayaranController::class, 'destroy'])->name('destroy');
    });

    // Archive Routes - TAMBAHKAN INI
    Route::prefix('archive')->name('archive.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ArchiveController::class, 'index'])->name('index');
        Route::get('/api', [App\Http\Controllers\Admin\ArchiveController::class, 'index'])->name('api');
        Route::get('/{id}', [App\Http\Controllers\Admin\ArchiveController::class, 'show'])->name('show');
        Route::post('/{id}', [App\Http\Controllers\Admin\ArchiveController::class, 'archive'])->name('store');
        Route::delete('/{id}', [App\Http\Controllers\Admin\ArchiveController::class, 'unarchive'])->name('unarchive');
        Route::post('/{id}/upload-final', [App\Http\Controllers\Admin\ArchiveController::class, 'uploadFinalDocument'])->name('upload-final');
        Route::get('/{id}/download/{type}', [App\Http\Controllers\Admin\ArchiveController::class, 'downloadDocument'])->name('download');
        Route::get('/export/data', [App\Http\Controllers\Admin\ArchiveController::class, 'exportData'])->name('export');
    });
}); 

require __DIR__ . '/auth.php';
