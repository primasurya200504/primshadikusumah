<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
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

Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::post('/submit', [UserController::class, 'submitForm'])->name('submit');
    Route::get('/guidelines', [UserController::class, 'showGuidelines'])->name('guidelines');
    Route::post('/upload-surat', [SuratController::class, 'uploadSurat'])->name('upload-surat');
    Route::get('/user/pembayaran/{surat_id}', [PembayaranController::class, 'showUser'])->name('user.pembayaran.show');
    Route::post('/user/pembayaran/{surat_id}/upload', [PembayaranController::class, 'uploadBukti'])->name('user.pembayaran.upload');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/requests', [AdminController::class, 'showRequests'])->name('requests');
    Route::get('/billing', [AdminController::class, 'showBilling'])->name('billing');
    Route::get('/users', [AdminController::class, 'showUsers'])->name('users');

    // Rute yang diperbaiki dan ditambahkan
    Route::patch('/submissions/{submission}/update-status', [AdminController::class, 'updateStatus'])->name('submissions.updateStatus');
    Route::patch('/submissions/{submission}/update-payment-status', [AdminController::class, 'updatePaymentStatus'])->name('submissions.updatePaymentStatus');

    Route::resource('guidelines', GuidelineController::class)->only(['index', 'store', 'update', 'destroy'])->names('guidelines');

    Route::get('/pembayaran/upload/{surat_id}', [PembayaranController::class, 'formUpload'])->name('admin.pembayaran.upload');
    Route::post('/pembayaran/upload/{surat_id}', [PembayaranController::class, 'uploadBillink'])->name('admin.pembayaran.upload.save');
    Route::get('/admin/pembayaran/{surat_id}/detail', [PembayaranController::class, 'showAdmin'])->name('admin.pembayaran.detail');
});

require __DIR__ . '/auth.php';
