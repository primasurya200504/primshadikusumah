<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Admin BMKG Pontianak</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            background-color: #f9fafb;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 500;
            border-radius: 20px;
        }

        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            transform: translateY(-1px);
        }

        /* TAMBAHAN: Styles untuk highlight pembayaran yang perlu verifikasi */
        .payment-needs-verification {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: none;
            border-radius: 12px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar (tetap sama) -->
        <div class="sidebar w-64 shadow-lg">
            <div class="flex items-center justify-center h-20 shadow-md">
                <h1 class="text-white text-xl font-bold">🌤️ BMKG Pontianak</h1>
            </div>
            <nav class="mt-10">
                <div class="px-4 py-2 text-gray-300 text-sm font-semibold uppercase tracking-wider">Menu Utama</div>
                <a class="flex items-center px-4 py-3 mt-2 text-gray-100 bg-white bg-opacity-20 rounded-r-full mr-4 transition-colors duration-200"
                    href="#">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m8 7V3a2 2 0 012-2h4a2 2 0 012 2v4"></path>
                    </svg>
                    Dasbor Admin
                </a>
                <a class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-white hover:bg-opacity-10 hover:text-white transition-colors duration-200 rounded-r-full mr-4"
                    href="#">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"></path>
                    </svg>
                    Manajemen Permintaan
                </a>
                <a class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-white hover:bg-opacity-10 hover:text-white transition-colors duration-200 rounded-r-full mr-4"
                    href="{{ route('admin.pembayaran.index') }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    Manajemen Pembayaran
                </a>
                <a class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-white hover:bg-opacity-10 hover:text-white transition-colors duration-200 rounded-r-full mr-4"
                    href="#">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Manajemen Panduan
                </a>
                <a class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-white hover:bg-opacity-10 hover:text-white transition-colors duration-200 rounded-r-full mr-4"
                    href="#">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"></path>
                    </svg>
                    Manajemen Pengguna
                </a>
            </nav>
            <div class="absolute bottom-0 w-64 px-4 py-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-3 text-red-300 hover:bg-red-600 hover:text-white transition-colors duration-200 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header (tetap sama) -->
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200">
                <div class="flex items-center">
                    <h1 class="text-2xl font-semibold text-gray-800">Dasbor Admin</h1>
                </div>
                <div class="flex items-center">
                    <div class="flex flex-col items-end mr-4">
                        <div class="text-sm font-semibold text-gray-700">Selamat datang, Admin!</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->role }}</div>
                    </div>
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-full p-2">
                        <span class="text-sm font-bold">A</span>
                    </div>
                </div>
            </header>

            <!-- Alert Messages (tetap sama) -->
            @if (session('success'))
                <div class="mx-6 mt-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-6 mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Dashboard Stats - DIPERBAIKI -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto main-content px-6 py-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <!-- Total Permintaan -->
                    <div class="card px-6 py-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Permintaan</dt>
                                    <dd class="text-2xl font-bold text-gray-900">{{ $totalSubmissions }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Permintaan data total.</p>
                        </div>
                    </div>

                    <!-- Permintaan Menunggu -->
                    <div class="card px-6 py-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-r from-yellow-500 to-yellow-600 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Permintaan Menunggu</dt>
                                    <dd class="text-2xl font-bold text-gray-900">{{ $pendingSubmissions }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Permintaan yang perlu diverifikasi.</p>
                        </div>
                    </div>

                    <!-- Pembayaran Tertunda -->
                    <div class="card px-6 py-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pembayaran Tertunda</dt>
                                    <dd class="text-2xl font-bold text-gray-900">{{ $pendingPayments ?? 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Pembayaran yang perlu dikonfirmasi.</p>
                        </div>
                    </div>

                    <!-- TAMBAHAN: Pembayaran Perlu Verifikasi -->
                    <div class="card px-6 py-6 border-l-4 border-orange-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">⚠️ Perlu Verifikasi</dt>
                                    <dd class="text-2xl font-bold text-orange-600">{{ $paidPayments ?? 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Bukti pembayaran user perlu diverifikasi.</p>
                        </div>
                    </div>
                </div>

                <!-- PERBAIKAN: Manajemen Pembayaran Section -->
                <div class="card mb-8">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Manajemen Pembayaran</h3>
                            <!-- TAMBAHAN: Filter buttons -->
                            <div class="flex space-x-2">
                                <button onclick="filterPayments('all')"
                                    class="filter-btn active px-3 py-1 text-xs bg-indigo-600 text-white rounded">Semua</button>
                                <button onclick="filterPayments('Dibayar')"
                                    class="filter-btn px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Perlu
                                    Verifikasi</button>
                                <button onclick="filterPayments('Menunggu Pembayaran')"
                                    class="filter-btn px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Menunggu
                                    Billing</button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nomor Pengajuan</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jenis Surat</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kategori Data</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pemohon</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Pengajuan</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status Surat</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pembayaran</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($submissions as $submission)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200 
                                             {{ $submission->pembayaran && $submission->pembayaran->status === 'Dibayar' ? 'payment-needs-verification' : '' }}"
                                            data-payment-status="{{ $submission->pembayaran ? $submission->pembayaran->status : 'no-payment' }}">

                                            <!-- Nomor Pengajuan -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $submission->submission_number }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $submission->created_at->format('d/m/Y H:i') }}</div>
                                            </td>

                                            <!-- Jenis Surat -->
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    {{ Str::limit($submission->data_type, 30) }}</div>
                                            </td>

                                            <!-- Kategori -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="status-badge {{ $submission->category === 'PNBP' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $submission->category }}
                                                </span>
                                            </td>

                                            <!-- Pemohon -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-medium text-gray-700">
                                                        {{ substr($submission->user->name, 0, 1) }}
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $submission->user->name }}</div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Tanggal Pengajuan -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $submission->created_at->format('d-m-Y') }}
                                            </td>

                                            <!-- Status Pengajuan -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($submission->status === 'Diterima')
                                                    <span class="status-badge bg-green-100 text-green-800">✅
                                                        Diterima</span>
                                                @elseif($submission->status === 'Menunggu Verifikasi')
                                                    <span class="status-badge bg-yellow-100 text-yellow-800">⏳ Menunggu
                                                        Verifikasi</span>
                                                @elseif($submission->status === 'Berhasil')
                                                    <span class="status-badge bg-blue-100 text-blue-800">🎉
                                                        Berhasil</span>
                                                @else
                                                    <span class="status-badge bg-red-100 text-red-800">❌
                                                        {{ $submission->status }}</span>
                                                @endif
                                            </td>

                                            <!-- PERBAIKAN: Status Pembayaran dengan Visual yang Jelas -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($submission->pembayaran)
                                                    @if ($submission->pembayaran->status === 'Menunggu Pembayaran')
                                                        <span class="status-badge bg-orange-100 text-orange-800">💰
                                                            Belum Dibayar</span>
                                                    @elseif($submission->pembayaran->status === 'Dibayar')
                                                        <span
                                                            class="status-badge bg-yellow-100 text-yellow-800 animate-pulse">
                                                            <span class="inline-flex items-center">
                                                                ⚠️ Perlu Verifikasi
                                                                <span
                                                                    class="ml-1 h-2 w-2 bg-yellow-400 rounded-full animate-ping"></span>
                                                            </span>
                                                        </span>
                                                    @elseif($submission->pembayaran->status === 'Terverifikasi')
                                                        <span class="status-badge bg-green-100 text-green-800">✅
                                                            Terverifikasi</span>
                                                    @else
                                                        <span
                                                            class="status-badge bg-gray-100 text-gray-800">{{ $submission->pembayaran->status }}</span>
                                                    @endif
                                                @else
                                                    <span class="status-badge bg-gray-100 text-gray-800">📋 Belum Ada
                                                        Billing</span>
                                                @endif
                                            </td>

                                            <!-- PERBAIKAN: Aksi dengan Tombol yang Jelas -->
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex flex-col space-y-1">
                                                    {{-- Jika pengajuan diterima --}}
                                                    @if ($submission->status === 'Diterima')
                                                        {{-- Upload Billing jika belum ada --}}
                                                        @if (!$submission->pembayaran || !$submission->pembayaran->billing_file_path)
                                                            <a href="{{ route('admin.pembayaran.upload', $submission->id) }}"
                                                                class="btn-action bg-indigo-600 hover:bg-indigo-700 text-white text-center">
                                                                📤 Upload Billing
                                                            </a>
                                                        @endif
                                                    @endif

                                                    {{-- PENTING: Jika ada bukti pembayaran dari user --}}
                                                    @if ($submission->pembayaran && $submission->pembayaran->payment_proof_path)
                                                        <a href="{{ route('admin.pembayaran.show', $submission->pembayaran->id) }}"
                                                            class="btn-action bg-green-600 hover:bg-green-700 text-white text-center animate-pulse">
                                                            🔍 Lihat Bukti Pembayaran
                                                        </a>
                                                    @endif

                                                    {{-- Update Status --}}
                                                    <button onclick="showStatusModal({{ json_encode($submission) }})"
                                                        class="btn-action bg-blue-600 hover:bg-blue-700 text-white text-center">
                                                        ⚙️ Update Status
                                                    </button>

                                                    {{-- Detail --}}
                                                    <button onclick="showDetails({{ json_encode($submission) }})"
                                                        class="btn-action bg-gray-600 hover:bg-gray-700 text-white text-center">
                                                        📄 Detail
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-12 text-center">
                                                <div class="text-gray-500">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                    <p class="text-lg font-medium">Tidak ada data pengajuan</p>
                                                    <p class="text-sm">Belum ada pengajuan yang masuk ke sistem.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal untuk Update Status (tetap sama dengan perbaikan) -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeStatusModal()">&times;</span>
            <h2 class="text-xl font-bold mb-4">Update Status Pengajuan</h2>
            <form id="statusForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Pengajuan</label>
                    <input type="text" id="modal-submission-number" readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Pengajuan</label>
                    <select name="status" id="modal-status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                        <option value="Diterima">Diterima</option>
                        <option value="Ditolak">Ditolak</option>
                        <option value="Berhasil">Berhasil</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                    <select name="payment_status" id="modal-payment-status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="Belum Dibayar">Belum Dibayar</option>
                        <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                        <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                        <option value="Sudah Dibayar">Sudah Dibayar</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeStatusModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk Detail (perbaikan) -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDetailModal()">&times;</span>
            <h2 class="text-xl font-bold mb-4">Detail Pengajuan</h2>
            <div id="detail-content">
                <!-- Content akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // PERBAIKAN: Enhanced JavaScript functionality

        // Filter payments by status
        function filterPayments(status) {
            const rows = document.querySelectorAll('tbody tr[data-payment-status]');
            const buttons = document.querySelectorAll('.filter-btn');

            // Update active button
            buttons.forEach(btn => {
                btn.classList.remove('active', 'bg-indigo-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });

            event.target.classList.add('active', 'bg-indigo-600', 'text-white');
            event.target.classList.remove('bg-gray-200', 'text-gray-700');

            // Filter rows
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-payment-status');
                if (status === 'all' || rowStatus === status) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Show status modal
        function showStatusModal(submission) {
            document.getElementById('statusModal').style.display = 'flex';
            document.getElementById('modal-submission-number').value = submission.submission_number;
            document.getElementById('modal-status').value = submission.status;
            document.getElementById('modal-payment-status').value = submission.payment_status || 'Belum Dibayar';
            document.getElementById('statusForm').action = `/admin/submission/${submission.id}/update-status`;
        }

        // Close status modal
        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }

        // Show detail modal dengan informasi lengkap
        function showDetails(submission) {
            document.getElementById('detailModal').style.display = 'flex';

            const content = document.getElementById('detail-content');
            const paymentInfo = submission.pembayaran ? `
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                    <p class="mt-1 text-sm text-gray-900">${submission.pembayaran.status}</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Bukti Pembayaran</label>
                    <p class="mt-1 text-sm text-gray-900">${submission.pembayaran.payment_proof_path ? '✅ Sudah diupload' : '❌ Belum diupload'}</p>
                </div>
            ` : '<p class="text-sm text-gray-500">Belum ada data pembayaran</p>';

            content.innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Pengajuan</label>
                            <p class="mt-1 text-sm text-gray-900">${submission.submission_number}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
                            <p class="mt-1 text-sm text-gray-900">${new Date(submission.created_at).toLocaleDateString('id-ID')}</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Data</label>
                        <p class="mt-1 text-sm text-gray-900">${submission.data_type}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <p class="mt-1 text-sm text-gray-900">${submission.category}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Keperluan</label>
                        <p class="mt-1 text-sm text-gray-900">${submission.purpose}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pemohon</label>
                        <p class="mt-1 text-sm text-gray-900">${submission.user ? submission.user.name : 'N/A'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Pengajuan</label>
                        <p class="mt-1 text-sm text-gray-900">${submission.status}</p>
                    </div>
                    <div class="border-t pt-4">
                        <h3 class="font-medium text-gray-900 mb-2">Informasi Pembayaran</h3>
                        ${paymentInfo}
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button onclick="closeDetailModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Tutup
                    </button>
                </div>
            `;
        }

        // Close detail modal
        function closeDetailModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const statusModal = document.getElementById('statusModal');
            const detailModal = document.getElementById('detailModal');

            if (event.target == statusModal) {
                statusModal.style.display = 'none';
            }
            if (event.target == detailModal) {
                detailModal.style.display = 'none';
            }
        }

        // TAMBAHAN: Auto refresh untuk highlight pembayaran baru
        setInterval(function() {
            // Optional: Add auto-refresh logic here if needed
        }, 30000); // 30 seconds
    </script>
</body>

</html>
