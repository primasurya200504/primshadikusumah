<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard BMKG Maritim Pontianak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
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
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 24px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Alert Payment Styles */
        .alert-payment {
            border-left: 4px solid #3b82f6;
            background-color: #eff6ff;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .alert-payment-success {
            border-left-color: #10b981;
            background-color: #ecfdf5;
        }

        .alert-payment-warning {
            border-left-color: #f59e0b;
            background-color: #fffbeb;
        }

        .action-button {
            transition: all 0.2s ease;
            font-size: 11px;
            padding: 4px 8px;
        }

        .action-button:hover {
            transform: translateY(-1px);
        }
    </style>
</head>

<body class="flex min-h-screen">
    @if ($errors->any())
        <script>
            window.onload = function() {
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += '<li>{{ $error }}</li>';
                @endforeach
                Swal.fire({
                    icon: 'error',
                    title: 'Pengajuan Gagal!',
                    html: `<ul>${errorMessages}</ul>`,
                });
            }
        </script>
    @endif

    <aside class="w-64 bg-white shadow-lg p-6 flex flex-col justify-between rounded-r-2xl">
        <div>
            <div class="flex items-center mb-10">
                <h1 class="text-xl font-bold ml-3 text-gray-800">BMKG Pontianak</h1>
            </div>
            <nav class="space-y-4">
                <a href="#dashboard" id="nav-dashboard"
                    class="flex items-center p-3 text-gray-600 hover:text-white hover:bg-indigo-600 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2-2m-2 2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dasbor
                </a>
                <a href="#pengajuan" id="nav-pengajuan"
                    class="flex items-center p-3 text-gray-600 hover:text-white hover:bg-indigo-600 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Pengajuan Surat
                </a>
                <a href="#panduan" id="nav-panduan"
                    class="flex items-center p-3 text-gray-600 hover:text-white hover:bg-indigo-600 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.468 9.587 5.097 8.323 5.097a2.796 2.796 0 00-.777.106V5.344a.796.796 0 00-.518-.755C6.012 4.382 4.67 4.195 3.328 4.195A2.796 2.796 0 00.552 4.41l.019.019V6.44c.54.496 1.15.828 1.83 1.012.68.184 1.41.282 2.16.282 1.342 0 2.684-.187 4.026-.563a.796.796 0 00.518-.755V5.344a.796.796 0 00.518-.755z">
                        </path>
                    </svg>
                    Panduan Surat/Data
                </a>
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center p-3 text-gray-600 hover:text-white hover:bg-indigo-600 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profil
                </a>
            </nav>
        </div>
        <div class="mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center w-full p-3 text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200">
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

    <main class="flex-1 p-8 overflow-y-auto">
        <header class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Selamat datang, {{ Auth::user()->name }}!</h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">{{ Auth::user()->role }}</span>
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}</div>
            </div>
        </header>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Alert untuk Status Pembayaran --}}
        @php
            $pendingBilling = $submissions->where('status', 'Diterima')->filter(function ($submission) {
                return !$submission->pembayaran || !$submission->pembayaran->billing_file_path;
            });
            $pendingPayment = $submissions->filter(function ($submission) {
                return $submission->pembayaran && $submission->pembayaran->status === 'Menunggu Pembayaran';
            });
        @endphp

        @if ($pendingBilling->count() > 0)
            <div class="alert-payment alert-payment-warning mb-6">
                <h4 class="font-semibold text-orange-800">⏳ Menunggu Billing</h4>
                <p class="text-orange-700 text-sm">
                    Anda memiliki {{ $pendingBilling->count() }} pengajuan yang sedang diproses billing oleh admin.
                </p>
            </div>
        @endif

        @if ($pendingPayment->count() > 0)
            <div class="alert-payment mb-6">
                <h4 class="font-semibold text-blue-800">💰 Pembayaran Tersedia</h4>
                <p class="text-blue-700 text-sm">
                    Anda memiliki {{ $pendingPayment->count() }} billing yang perlu dibayar.
                    Silakan download billing dan upload bukti pembayaran.
                </p>
            </div>
        @endif

        <section id="dashboard" class="content-section active">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h3 class="text-2xl font-bold mb-4">Dasbor</h3>
                <p class="text-gray-600 mb-6">Berikut adalah riwayat pengajuan surat/data Anda.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead>
                            <tr class="bg-gray-200 text-left text-sm font-semibold text-gray-700">
                                <th class="py-3 px-4 rounded-tl-lg">No. Surat</th>
                                <th class="py-3 px-4">Tanggal Pengajuan</th>
                                <th class="py-3 px-4">Jenis Data</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Status Pembayaran</th>
                                <th class="py-3 px-4">Surat Pengantar</th>
                                <th class="py-3 px-4 rounded-tr-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-normal">
                            @forelse ($submissions as $submission)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-4">{{ $submission->submission_number }}</td>
                                    <td class="py-3 px-4">{{ $submission->created_at->format('Y-m-d') }}</td>
                                    <td class="py-3 px-4">{{ $submission->data_type }}</td>

                                    {{-- Status Pengajuan --}}
                                    <td class="py-3 px-4">
                                        <span
                                            class="
                                            @if ($submission->status === 'Berhasil') bg-green-100 text-green-700
                                            @elseif ($submission->status === 'Diterima') bg-blue-100 text-blue-700
                                            @elseif ($submission->status === 'Menunggu Verifikasi') bg-yellow-100 text-yellow-700
                                            @elseif ($submission->status === 'Ditolak') bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-700 @endif
                                            font-medium py-1 px-3 rounded-full text-xs">
                                            {{ $submission->status }}
                                        </span>
                                    </td>

                                    {{-- Status Pembayaran --}}
                                    <td class="py-3 px-4">
                                        @if ($submission->pembayaran)
                                            <span
                                                class="
                                                @if ($submission->pembayaran->status === 'Selesai') bg-green-100 text-green-700
                                                @elseif($submission->pembayaran->status === 'Terverifikasi') bg-blue-100 text-blue-700
                                                @elseif($submission->pembayaran->status === 'Dibayar') bg-yellow-100 text-yellow-700
                                                @elseif($submission->pembayaran->status === 'Menunggu Pembayaran') bg-orange-100 text-orange-700
                                                @else bg-red-100 text-red-700 @endif
                                                font-medium py-1 px-3 rounded-full text-xs">
                                                {{ $submission->pembayaran->status }}
                                            </span>
                                        @else
                                            <span
                                                class="bg-gray-100 text-gray-700 font-medium py-1 px-3 rounded-full text-xs">
                                                Belum Ada Billing
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Surat Pengantar --}}
                                    <td class="py-3 px-4">
                                        @if ($submission->files && $submission->files->count())
                                            @foreach ($submission->files as $file)
                                                <div>
                                                    <a href="{{ asset('storage/' . $file->file_path) }}"
                                                        target="_blank" class="text-blue-600 hover:underline">
                                                        {{ $file->file_name ?? 'Lihat Surat' }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Aksi Pembayaran --}}
                                    <td class="py-3 px-4">
                                        <div class="flex flex-col space-y-1">
                                            {{-- Jika pengajuan diterima dan ada billing --}}
                                            @if ($submission->status === 'Diterima' && $submission->pembayaran)
                                                {{-- Download Billing --}}
                                                @if ($submission->pembayaran->billing_file_path)
                                                    <a href="{{ route('user.pembayaran.download', $submission->pembayaran->id) }}"
                                                        class="action-button bg-blue-500 hover:bg-blue-600 text-white text-center rounded">
                                                        📄 Download Billing
                                                    </a>

                                                    {{-- Upload Bukti Pembayaran --}}
                                                    @if (!$submission->pembayaran->payment_proof_path)
                                                        <a href="{{ route('user.pembayaran.upload', $submission->id) }}"
                                                            class="action-button bg-green-500 hover:bg-green-600 text-white text-center rounded">
                                                            📤 Upload Bukti
                                                        </a>
                                                    @else
                                                        <span class="text-green-600 text-xs text-center">✅ Bukti
                                                            Terkirim</span>
                                                    @endif
                                                @else
                                                    <span class="text-orange-600 text-xs text-center">⏳ Menunggu
                                                        Billing</span>
                                                @endif

                                                {{-- Jika pengajuan berhasil --}}
                                            @elseif ($submission->status === 'Berhasil')
                                                <a href="{{ asset('storage/' . $submission->cover_letter_path) }}"
                                                    target="_blank"
                                                    class="action-button bg-indigo-500 hover:bg-indigo-600 text-white text-center rounded">
                                                    📥 Unduh Data
                                                </a>

                                                {{-- Jika pengajuan ditolak --}}
                                            @elseif ($submission->status === 'Ditolak')
                                                <div class="flex flex-col space-y-1">
                                                    <a href="#"
                                                        onclick="showEditModal({{ json_encode($submission) }})"
                                                        class="action-button bg-gray-500 hover:bg-gray-600 text-white text-center rounded">
                                                        ✏️ Edit
                                                    </a>
                                                    <a href="https://wa.me/{{ $submission->user->phone_number ?? '6281234567890' }}?text=Halo%20Admin,%20saya%20ingin%20mengubah%20data%20pengajuan%20dengan%20nomor%20surat%20{{ $submission->submission_number }}.%20Catatan%20Penolakan:%20{{ $submission->rejection_note }}"
                                                        target="_blank"
                                                        class="action-button bg-green-500 hover:bg-green-600 text-white text-center rounded">
                                                        💬 Hubungi WA
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-gray-500 text-xs text-center">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 px-4 text-center text-gray-500">
                                        Anda belum memiliki riwayat pengajuan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="pengajuan" class="content-section hidden">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h3 class="text-2xl font-bold mb-4">Formulir Pengajuan Surat/Data</h3>

                <p class="text-sm text-gray-500 mb-4">
                    Tidak punya surat pengantar? Unduh contohnya di sini:
                </p>
                <div class="mb-6">
                    <h4 class="font-semibold mb-2">Tabel Unduhan Surat Pengantar</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead>
                                <tr class="bg-gray-100 text-sm">
                                    <th class="py-2 px-4 border-b text-left">Jenis Surat</th>
                                    <th class="py-2 px-4 border-b text-left">Keterangan</th>
                                    <th class="py-2 px-4 border-b text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">Surat Pengantar Umum</td>
                                    <td class="py-2 px-4 border-b">Untuk pengajuan data secara umum atau keperluan
                                        pribadi.</td>
                                    <td class="py-2 px-4 border-b">
                                        <a href="#" class="text-blue-600 hover:underline font-medium">Unduh
                                            .docx</a>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">Surat Pengantar Penelitian</td>
                                    <td class="py-2 px-4 border-b">Khusus untuk mahasiswa atau peneliti.</td>
                                    <td class="py-2 px-4 border-b">
                                        <a href="#" class="text-blue-600 hover:underline font-medium">Unduh
                                            .docx</a>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">Surat Pengantar Instansi</td>
                                    <td class="py-2 px-4 border-b">Untuk pengajuan resmi dari instansi
                                        pemerintah/swasta.</td>
                                    <td class="py-2 px-4 border-b">
                                        <a href="#" class="text-blue-600 hover:underline font-medium">Unduh
                                            .docx</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-2">Kategori Pengajuan</h4>
                    <div class="flex space-x-4">
                        <button type="button" id="btn-pnbp"
                            class="px-6 py-2 rounded-lg bg-indigo-600 text-white font-semibold shadow-md transition-colors duration-200 hover:bg-indigo-700">PNBP</button>
                        <button type="button" id="btn-nonpnbp"
                            class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold shadow-md transition-colors duration-200 hover:bg-gray-300">Non-PNBP</button>
                    </div>
                </div>
                <form id="submission-form" action="{{ route('user.submit') }}" method="POST"
                    enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="kategori" id="kategori_input" value="PNBP">

                    <div>
                        <label for="jenis_data" class="block text-gray-700 font-semibold mb-2">Jenis Data yang
                            Diajukan</label>
                        <select id="jenis_data" name="jenis_data"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Jenis Data</option>
                            @foreach ($guidelines as $guideline)
                                <option value="{{ $guideline->title }}">{{ $guideline->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tanggal_mulai" class="block text-gray-700 font-semibold mb-2">Tanggal
                            Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-gray-700 font-semibold mb-2">Tanggal
                            Selesai</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="keperluan" class="block text-gray-700 font-semibold mb-2">Keperluan Penggunaan
                            Data</label>
                        <textarea id="keperluan" name="keperluan" rows="4"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    {{-- Kontainer input file PNBP --}}
                    <div id="file-upload-pnbp">
                        <div>
                            <label for="file_surat_pnbp" class="block text-gray-700 font-semibold mb-2">Upload Surat
                                Pengantar</label>
                            <input type="file" id="file_surat_pnbp" name="files[]"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Kontainer input file Non-PNBP --}}
                    <div id="file-upload-nonpnbp" class="hidden space-y-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Upload Surat Pengantar dari
                                Instansi</label>
                            <input type="file" name="files[]"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Upload Dokumen Proposal/Karya
                                Ilmiah</label>
                            <input type="file" name="files[]"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Upload Dokumen Pendukung
                                Lainnya</label>
                            <input type="file" name="files[]"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Upload Dokumen Pendukung Lainnya
                                (Opsional)</label>
                            <input type="file" name="files[]"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full p-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-colors duration-200">Ajukan
                        Surat</button>
                </form>
            </div>
        </section>

        <section id="panduan" class="content-section hidden">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h3 class="text-2xl font-bold mb-4">Panduan Pengajuan Surat/Data</h3>
                <p class="text-gray-600 mb-6">Klik pada jenis data di bawah ini untuk melihat detail, contoh, dan
                    syarat pengajuannya.</p>

                <div id="accordion-container" class="space-y-4">
                    @forelse ($guidelines as $guideline)
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button
                                class="accordion-header w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-800">{{ $guideline->title }}</span>
                                <svg class="w-6 h-6 transform transition-transform duration-200" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="accordion-content hidden p-4 bg-white text-gray-700">
                                <p class="mb-4">{{ $guideline->content }}</p>
                                @if (isset($guideline->example_data) && is_array($guideline->example_data) && !empty($guideline->example_data))
                                    <h4 class="font-bold mb-2">Contoh Data:</h4>
                                    <div class="overflow-x-auto mb-4">
                                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                            <thead>
                                                <tr class="bg-gray-100 text-sm">
                                                    @foreach (array_keys($guideline->example_data[0]) as $key)
                                                        <th class="py-2 px-4 border-b">{{ $key }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($guideline->example_data as $data)
                                                    <tr class="hover:bg-gray-50">
                                                        @foreach ($data as $value)
                                                            <td class="py-2 px-4 border-b">{{ $value }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                @if (isset($guideline->requirements) && is_array($guideline->requirements) && !empty($guideline->requirements))
                                    <h4 class="font-bold mb-2">Syarat Pengajuan:</h4>
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($guideline->requirements as $requirement)
                                            <li>{{ $requirement }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500">Belum ada panduan yang ditambahkan oleh Admin.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h4 class="text-xl font-bold mb-4">Edit Pengajuan</h4>
            <form id="editForm" class="space-y-4" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_submission_id" name="submission_id">
                <div>
                    <label for="edit_no_surat" class="block text-gray-700 font-semibold mb-2">No. Surat</label>
                    <input type="text" id="edit_no_surat"
                        class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
                </div>
                <div>
                    <label for="edit_jenis_data" class="block text-gray-700 font-semibold mb-2">Jenis Data</label>
                    <select id="edit_jenis_data" name="jenis_data"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach ($guidelines as $guideline)
                            <option value="{{ $guideline->title }}">{{ $guideline->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_tanggal_mulai" class="block text-gray-700 font-semibold mb-2">Tanggal
                        Mulai</label>
                    <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="edit_tanggal_selesai" class="block text-gray-700 font-semibold mb-2">Tanggal
                        Selesai</label>
                    <input type="date" id="edit_tanggal_selesai" name="tanggal_selesai"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="edit_keperluan" class="block text-gray-700 font-semibold mb-2">Keperluan</label>
                    <textarea id="edit_keperluan" name="keperluan" rows="4"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label for="edit_surat_pengantar" class="block text-gray-700 font-semibold mb-2">Unggah Ulang
                        Surat Pengantar</label>
                    <input type="file" id="edit_surat_pengantar" name="surat_pengantar"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit"
                    class="w-full p-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-colors duration-200">Simpan
                    Perubahan</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('a[id^="nav-"]');
            const sections = document.querySelectorAll('.content-section');
            const editModal = document.getElementById('editModal');
            const accordionHeaders = document.querySelectorAll('.accordion-header');

            const showSection = (id) => {
                sections.forEach(section => {
                    section.classList.add('hidden');
                });
                const targetSection = document.getElementById(id);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }

                navLinks.forEach(link => {
                    link.classList.remove('text-white', 'bg-indigo-600');
                    link.classList.add('text-gray-600', 'hover:bg-indigo-600');
                });
                const targetNavLink = document.getElementById(`nav-${id}`);
                if (targetNavLink) {
                    targetNavLink.classList.add('text-white', 'bg-indigo-600');
                    targetNavLink.classList.remove('text-gray-600', 'hover:bg-indigo-600');
                }
            };

            navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = e.currentTarget.getAttribute('href').substring(1);
                    showSection(targetId);
                });
            });

            const initialHash = window.location.hash.substring(1) || 'dashboard';
            showSection(initialHash);

            window.showEditModal = (submissionData) => {
                editModal.style.display = 'flex';
                document.getElementById('edit_no_surat').value = submissionData.submission_number;
                document.getElementById('edit_submission_id').value = submissionData.id;
                document.getElementById('edit_jenis_data').value = submissionData.data_type;
                document.getElementById('edit_tanggal_mulai').value = submissionData.start_date;
                document.getElementById('edit_tanggal_selesai').value = submissionData.end_date;
                document.getElementById('edit_keperluan').value = submissionData.purpose;

                document.getElementById('editForm').action = `/user/submissions/${submissionData.id}`;
            };

            window.closeEditModal = () => {
                editModal.style.display = 'none';
            };

            accordionHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const content = header.nextElementSibling;
                    const svg = header.querySelector('svg');
                    content.classList.toggle('hidden');
                    svg.classList.toggle('rotate-180');
                });
            });

            const pnbpBtn = document.getElementById('btn-pnbp');
            const nonpnbpBtn = document.getElementById('btn-nonpnbp');
            const fileUploadPNBP = document.getElementById('file-upload-pnbp');
            const pnbpFileInput = fileUploadPNBP.querySelector('input[type="file"]');
            const nonpnbpFilesContainer = document.getElementById('file-upload-nonpnbp');
            const nonpnbpFileInputs = nonpnbpFilesContainer.querySelectorAll('input[type="file"]');
            const kategoriInput = document.getElementById('kategori_input');

            const toggleFileInputs = (pnbpActive) => {
                if (pnbpActive) {
                    fileUploadPNBP.classList.remove('hidden');
                    nonpnbpFilesContainer.classList.add('hidden');
                    pnbpFileInput.removeAttribute('disabled');
                    nonpnbpFileInputs.forEach(input => input.setAttribute('disabled', 'disabled'));
                } else {
                    fileUploadPNBP.classList.add('hidden');
                    nonpnbpFilesContainer.classList.remove('hidden');
                    pnbpFileInput.setAttribute('disabled', 'disabled');
                    nonpnbpFileInputs.forEach(input => input.removeAttribute('disabled'));
                }
            };

            pnbpBtn.addEventListener('click', () => {
                toggleFileInputs(true);
                pnbpBtn.classList.add('bg-indigo-600', 'text-white');
                pnbpBtn.classList.remove('bg-gray-200', 'text-gray-700');
                nonpnbpBtn.classList.remove('bg-indigo-600', 'text-white');
                nonpnbpBtn.classList.add('bg-gray-200', 'text-gray-700');
                kategoriInput.value = 'PNBP';
            });

            nonpnbpBtn.addEventListener('click', () => {
                toggleFileInputs(false);
                nonpnbpBtn.classList.add('bg-indigo-600', 'text-white');
                nonpnbpBtn.classList.remove('bg-gray-200', 'text-gray-700');
                pnbpBtn.classList.remove('bg-indigo-600', 'text-white');
                pnbpBtn.classList.add('bg-gray-200', 'text-gray-700');
                kategoriInput.value = 'Non-PNBP';
            });

            // Set initial state
            toggleFileInputs(true);

            // Auto-refresh untuk status pembayaran setiap 30 detik
            setInterval(() => {
                const currentUrl = window.location.href;
                if (currentUrl.includes('#dashboard')) {
                    console.log('Checking payment status...');
                    // Optional: Implementasi refresh otomatis status
                }
            }, 30000);
        });
    </script>
</body>

</html>
