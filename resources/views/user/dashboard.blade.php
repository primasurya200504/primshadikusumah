@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-lg min-h-screen">
                <div class="p-6">
                    <div class="flex items-center mb-8">
                        <svg class="w-8 h-8 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                        <h1 class="text-xl font-bold text-gray-800">BMKG Pontianak</h1>
                    </div>

                    <nav class="space-y-2">
                        <a href="#dashboard" onclick="showSection('dashboard')"
                            class="nav-link flex items-center p-3 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2-2m-2 2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            Dasbor
                        </a>

                        <a href="#pengajuan" onclick="showSection('pengajuan')"
                            class="nav-link flex items-center p-3 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Pengajuan Surat
                        </a>

                        <a href="#panduan" onclick="showSection('panduan')"
                            class="nav-link flex items-center p-3 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.468 9.587 5.097 8.323 5.097c-.31 0-.612.15-.746.25L6.5 6.44v6.44a1.5 1.5 0 001.5 1.5c.31 0 .612-.15.746-.25L12 12.44v1.81z">
                                </path>
                            </svg>
                            Panduan
                        </a>

                        <a href="{{ route('profile.edit') }}"
                            class="nav-link flex items-center p-3 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profil
                        </a>
                    </nav>
                </div>

                <div class="absolute bottom-6 left-10">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center p-3 text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-8">
                <!-- Header -->
                <header class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">Selamat datang, {{ Auth::user()->name }}!</h2>
                        <p class="text-gray-600 mt-1">Dashboard Pengajuan Data BMKG</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span
                            class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">{{ Auth::user()->role }}</span>
                        <div
                            class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                </header>

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Payment Status Alerts -->
                @php
                    $pendingBilling = $submissions->where('status', 'Diterima')->filter(function ($submission) {
                        return !$submission->pembayaran || !$submission->pembayaran->billing_file_path;
                    });
                    $pendingPayment = $submissions->filter(function ($submission) {
                        return $submission->pembayaran &&
                            $submission->pembayaran->billing_file_path &&
                            !$submission->pembayaran->payment_proof_path;
                    });
                @endphp

                @if ($pendingBilling->count() > 0)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Menunggu Billing:</strong> {{ $pendingBilling->count() }} pengajuan sedang
                                    diproses billing oleh admin.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($pendingPayment->count() > 0)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Pembayaran Tersedia:</strong> {{ $pendingPayment->count() }} billing siap untuk
                                    dibayar. Silakan download dan upload bukti pembayaran.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Dashboard Section -->
                <section id="dashboard" class="content-section">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-semibold mb-4">Statistik Pengajuan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-blue-600">Total Pengajuan</p>
                                        <p class="text-2xl font-bold text-blue-900">{{ $submissions->count() }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-yellow-600">Menunggu</p>
                                        <p class="text-2xl font-bold text-yellow-900">
                                            {{ $submissions->where('status', 'Menunggu Verifikasi')->count() }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-green-600">Diterima</p>
                                        <p class="text-2xl font-bold text-green-900">
                                            {{ $submissions->whereIn('status', ['Diterima', 'Berhasil'])->count() }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-red-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-red-600">Ditolak</p>
                                        <p class="text-2xl font-bold text-red-900">
                                            {{ $submissions->where('status', 'Ditolak')->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Riwayat Pengajuan -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4">Riwayat Pengajuan</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No. Pengajuan</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jenis Data</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pembayaran</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($submissions as $submission)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $submission->submission_number ?? 'SUB-' . $submission->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $submission->created_at->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $submission->data_type ?? 'Tidak diketahui' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if ($submission->status === 'Berhasil') bg-green-100 text-green-800
                                                @elseif($submission->status === 'Diterima') bg-blue-100 text-blue-800
                                                @elseif($submission->status === 'Menunggu Verifikasi') bg-yellow-100 text-yellow-800
                                                @elseif($submission->status === 'Ditolak') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $submission->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($submission->pembayaran)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if ($submission->pembayaran->status === 'Selesai') bg-green-100 text-green-800
                                                    @elseif($submission->pembayaran->status === 'Terverifikasi') bg-blue-100 text-blue-800
                                                    @elseif($submission->pembayaran->status === 'Dibayar') bg-yellow-100 text-yellow-800
                                                    @elseif($submission->pembayaran->status === 'Menunggu Pembayaran') bg-orange-100 text-orange-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                        {{ $submission->pembayaran->status }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Belum Ada Billing
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    @if ($submission->status === 'Diterima' && $submission->pembayaran)
                                                        @if ($submission->pembayaran->billing_file_path)
                                                            <a href="{{ route('user.pembayaran.download', $submission->pembayaran->id) }}"
                                                                class="text-indigo-600 hover:text-indigo-900">Download
                                                                Billing</a>

                                                            @if (!$submission->pembayaran->payment_proof_path)
                                                                <a href="{{ route('user.pembayaran.upload', $submission->id) }}"
                                                                    class="text-green-600 hover:text-green-900">Upload
                                                                    Bukti</a>
                                                            @endif
                                                        @endif
                                                    @elseif ($submission->status === 'Berhasil')
                                                        <a href="{{ asset('storage/' . $submission->cover_letter_path) }}"
                                                            target="_blank"
                                                            class="text-indigo-600 hover:text-indigo-900">Download Data</a>
                                                    @elseif ($submission->status === 'Ditolak')
                                                        <button onclick="showEditModal({{ json_encode($submission) }})"
                                                            class="text-gray-600 hover:text-gray-900">Edit</button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                                Belum ada pengajuan. <a href="#pengajuan"
                                                    onclick="showSection('pengajuan')"
                                                    class="text-indigo-600 hover:text-indigo-900">Buat pengajuan baru</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Pengajuan Section -->
                <section id="pengajuan" class="content-section hidden">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-2xl font-bold mb-6">Formulir Pengajuan Surat/Data</h3>

                        <!-- Template Downloads -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-semibold mb-3 text-blue-900">📄 Template Surat Pengantar</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="bg-white p-4 rounded border border-blue-200">
                                        <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <h5 class="font-medium text-blue-900">Surat Umum</h5>
                                        <p class="text-sm text-blue-700 mb-2">Keperluan pribadi/umum</p>
                                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">Download
                                            .docx</a>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="bg-white p-4 rounded border border-blue-200">
                                        <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.468 9.587 5.097 8.323 5.097c-.31 0-.612.15-.746.25L6.5 6.44v6.44a1.5 1.5 0 001.5 1.5c.31 0 .612-.15.746-.25L12 12.44v1.81z" />
                                        </svg>
                                        <h5 class="font-medium text-blue-900">Surat Penelitian</h5>
                                        <p class="text-sm text-blue-700 mb-2">Mahasiswa/peneliti</p>
                                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">Download
                                            .docx</a>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="bg-white p-4 rounded border border-blue-200">
                                        <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <h5 class="font-medium text-blue-900">Surat Instansi</h5>
                                        <p class="text-sm text-blue-700 mb-2">Keperluan resmi instansi</p>
                                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">Download
                                            .docx</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kategori Selection -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-700 mb-3">Kategori Pengajuan</h4>
                            <div class="flex space-x-4">
                                <button type="button" id="btn-pnbp"
                                    class="px-6 py-2 rounded-lg bg-indigo-600 text-white font-semibold shadow transition-colors duration-200 hover:bg-indigo-700">
                                    PNBP (Berbayar)
                                </button>
                                <button type="button" id="btn-nonpnbp"
                                    class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold shadow transition-colors duration-200 hover:bg-gray-300">
                                    Non-PNBP (Gratis)
                                </button>
                            </div>
                        </div>

                        <!-- Form Pengajuan -->
                        <form action="{{ route('user.submit') }}" method="POST" enctype="multipart/form-data"
                            class="space-y-6">
                            @csrf
                            <input type="hidden" name="kategori" id="kategori_input" value="PNBP">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="jenis_data" class="block text-sm font-medium text-gray-700 mb-2">Jenis
                                        Data yang Diajukan</label>
                                    <select id="jenis_data" name="jenis_data" required
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Pilih Jenis Data</option>
                                        @if ($guidelines && $guidelines->count() > 0)
                                            @foreach ($guidelines as $guideline)
                                                <option value="{{ $guideline->title }}">{{ $guideline->title }}</option>
                                            @endforeach
                                        @else
                                            <option value="Data Cuaca Harian">Data Cuaca Harian</option>
                                            <option value="Data Iklim Bulanan">Data Iklim Bulanan</option>
                                            <option value="Data Gempa">Data Gempa</option>
                                        @endif
                                    </select>
                                </div>

                                <div>
                                    <label for="keperluan" class="block text-sm font-medium text-gray-700 mb-2">Keperluan
                                        Penggunaan Data</label>
                                    <textarea id="keperluan" name="keperluan" rows="3" required
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Jelaskan keperluan penggunaan data..."></textarea>
                                </div>

                                <div>
                                    <label for="tanggal_mulai"
                                        class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" required
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label for="tanggal_selesai"
                                        class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" required
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <!-- File Upload Section -->
                            <div id="file-upload-pnbp" class="space-y-4">
                                <div>
                                    <label for="file_surat_pnbp"
                                        class="block text-sm font-medium text-gray-700 mb-2">Upload Surat Pengantar
                                        *</label>
                                    <input type="file" id="file_surat_pnbp" name="files[]" accept=".pdf,.doc,.docx"
                                        required
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX (Max: 5MB)</p>
                                </div>
                            </div>

                            <div id="file-upload-nonpnbp" class="hidden space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Surat Pengantar dari
                                        Instansi *</label>
                                    <input type="file" name="files[]" accept=".pdf,.doc,.docx"
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX (Max: 5MB)</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen
                                        Proposal/Karya Ilmiah</label>
                                    <input type="file" name="files[]" accept=".pdf,.doc,.docx"
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX (Max: 5MB)</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen Pendukung
                                        Lainnya (Opsional)</label>
                                    <input type="file" name="files[]" accept=".pdf,.doc,.docx"
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX (Max: 5MB)</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end space-x-4">
                                <button type="reset"
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                    Reset Form
                                </button>
                                <button type="submit"
                                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200 font-semibold">
                                    Ajukan Surat
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Panduan Section -->
                <section id="panduan" class="content-section hidden">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-2xl font-bold mb-6">Panduan Pengajuan Surat/Data</h3>
                        <p class="text-gray-600 mb-6">Klik pada jenis data di bawah ini untuk melihat detail, contoh, dan
                            syarat pengajuannya.</p>

                        <div class="space-y-4">
                            @forelse ($guidelines as $guideline)
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <button
                                        class="accordion-header w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition-colors duration-200"
                                        onclick="toggleAccordion(this)">
                                        <span class="text-lg font-semibold text-gray-800">{{ $guideline->title }}</span>
                                        <svg class="w-6 h-6 transform transition-transform duration-200" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="accordion-content hidden p-4 bg-white">
                                        <p class="text-gray-700 mb-4">{{ $guideline->content }}</p>

                                        @if ($guideline->requirements && is_array($guideline->requirements))
                                            <h4 class="font-bold mb-2 text-gray-800">Syarat Pengajuan:</h4>
                                            <ul class="list-disc list-inside space-y-1 mb-4 text-gray-700">
                                                @foreach ($guideline->requirements as $requirement)
                                                    <li>{{ $requirement }}</li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        @if ($guideline->example_data && is_array($guideline->example_data))
                                            <h4 class="font-bold mb-2 text-gray-800">Contoh Data:</h4>
                                            <div class="bg-gray-50 p-4 rounded-lg">
                                                @foreach ($guideline->example_data as $key => $value)
                                                    <p class="text-sm text-gray-600"><strong>{{ ucfirst($key) }}:</strong>
                                                        {{ $value }}</p>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.468 9.587 5.097 8.323 5.097c-.31 0-.612.15-.746.25L6.5 6.44v6.44a1.5 1.5 0 001.5 1.5c.31 0 .612-.15.746-.25L12 12.44v1.81z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Panduan</h3>
                                    <p class="text-gray-500">Panduan akan ditambahkan oleh admin segera.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Modal Edit Pengajuan -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-xl font-bold text-gray-900">Edit Pengajuan</h4>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="editForm" class="space-y-4" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_submission_id" name="submission_id">

                <div>
                    <label for="edit_no_surat" class="block text-sm font-medium text-gray-700 mb-2">No. Surat</label>
                    <input type="text" id="edit_no_surat"
                        class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
                </div>

                <div>
                    <label for="edit_jenis_data" class="block text-sm font-medium text-gray-700 mb-2">Jenis Data</label>
                    <select id="edit_jenis_data" name="jenis_data"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @if ($guidelines && $guidelines->count() > 0)
                            @foreach ($guidelines as $guideline)
                                <option value="{{ $guideline->title }}">{{ $guideline->title }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label for="edit_tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                        Mulai</label>
                    <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="edit_tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                        Selesai</label>
                    <input type="date" id="edit_tanggal_selesai" name="tanggal_selesai"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="edit_keperluan" class="block text-sm font-medium text-gray-700 mb-2">Keperluan</label>
                    <textarea id="edit_keperluan" name="keperluan" rows="3"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div>
                    <label for="edit_surat_pengantar" class="block text-sm font-medium text-gray-700 mb-2">Upload Ulang
                        Surat Pengantar</label>
                    <input type="file" id="edit_surat_pengantar" name="surat_pengantar" accept=".pdf,.doc,.docx"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-sm text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah file</p>
                </div>

                <div class="flex space-x-4">
                    <button type="button" onclick="closeEditModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navigation
            window.showSection = function(sectionId) {
                // Hide all sections
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.add('hidden');
                });

                // Show target section
                const targetSection = document.getElementById(sectionId);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }

                // Update navigation
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('text-indigo-600', 'bg-indigo-50');
                    link.classList.add('text-gray-600');
                });

                const targetNav = document.querySelector(`a[onclick="showSection('${sectionId}')"]`);
                if (targetNav) {
                    targetNav.classList.add('text-indigo-600', 'bg-indigo-50');
                    targetNav.classList.remove('text-gray-600');
                }
            };

            // Initialize
            showSection('dashboard');

            // Category buttons
            const pnbpBtn = document.getElementById('btn-pnbp');
            const nonpnbpBtn = document.getElementById('btn-nonpnbp');
            const fileUploadPNBP = document.getElementById('file-upload-pnbp');
            const fileUploadNonPNBP = document.getElementById('file-upload-nonpnbp');
            const kategoriInput = document.getElementById('kategori_input');

            if (pnbpBtn && nonpnbpBtn) {
                pnbpBtn.addEventListener('click', function() {
                    // Update buttons
                    pnbpBtn.classList.add('bg-indigo-600', 'text-white');
                    pnbpBtn.classList.remove('bg-gray-200', 'text-gray-700');
                    nonpnbpBtn.classList.remove('bg-indigo-600', 'text-white');
                    nonpnbpBtn.classList.add('bg-gray-200', 'text-gray-700');

                    // Update form
                    fileUploadPNBP.classList.remove('hidden');
                    fileUploadNonPNBP.classList.add('hidden');
                    kategoriInput.value = 'PNBP';

                    // Enable/disable inputs
                    fileUploadPNBP.querySelectorAll('input').forEach(input => input.removeAttribute(
                        'disabled'));
                    fileUploadNonPNBP.querySelectorAll('input').forEach(input => input.setAttribute(
                        'disabled', 'disabled'));
                });

                nonpnbpBtn.addEventListener('click', function() {
                    // Update buttons
                    nonpnbpBtn.classList.add('bg-indigo-600', 'text-white');
                    nonpnbpBtn.classList.remove('bg-gray-200', 'text-gray-700');
                    pnbpBtn.classList.remove('bg-indigo-600', 'text-white');
                    pnbpBtn.classList.add('bg-gray-200', 'text-gray-700');

                    // Update form
                    fileUploadPNBP.classList.add('hidden');
                    fileUploadNonPNBP.classList.remove('hidden');
                    kategoriInput.value = 'Non-PNBP';

                    // Enable/disable inputs
                    fileUploadPNBP.querySelectorAll('input').forEach(input => input.setAttribute('disabled',
                        'disabled'));
                    fileUploadNonPNBP.querySelectorAll('input').forEach(input => input.removeAttribute(
                        'disabled'));
                });
            }

            // Accordion
            window.toggleAccordion = function(button) {
                const content = button.nextElementSibling;
                const svg = button.querySelector('svg');

                content.classList.toggle('hidden');
                svg.classList.toggle('rotate-180');
            };

            // Edit Modal
            window.showEditModal = function(submissionData) {
                const modal = document.getElementById('editModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                // Fill form
                document.getElementById('edit_no_surat').value = submissionData.submission_number || 'SUB-' +
                    submissionData.id;
                document.getElementById('edit_submission_id').value = submissionData.id;
                document.getElementById('edit_jenis_data').value = submissionData.data_type || '';
                document.getElementById('edit_tanggal_mulai').value = submissionData.start_date || '';
                document.getElementById('edit_tanggal_selesai').value = submissionData.end_date || '';
                document.getElementById('edit_keperluan').value = submissionData.purpose || '';

                // Set form action
                document.getElementById('editForm').action = `/user/submissions/${submissionData.id}`;
            };

            window.closeEditModal = function() {
                const modal = document.getElementById('editModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            // Close modal on outside click
            document.getElementById('editModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditModal();
                }
            });
        });
    </script>
@endsection
