<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dasbor Admin BMKG Pontianak</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
        }

        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-right: 1px solid #374151;
        }

        .main-content {
            background-color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 500;
            border-radius: 9999px;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 50;
        }

        .modal-content {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.75rem;
            width: 90%;
            max-width: 768px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-height: 90vh;
            overflow-y: auto;
        }

        .nav-link {
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            transform: translateX(5px);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid #fff;
        }

        .card {
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-gray-100 flex h-screen">

    <!-- Sidebar -->
    <div class="w-64 sidebar text-white p-6 flex flex-col">
        <div class="flex items-center space-x-2 mb-8">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            <span class="text-xl font-semibold">BMKG Pontianak</span>
        </div>

        <div class="text-sm text-gray-200 mb-6">MENU UTAMA</div>

        <nav class="space-y-2 flex-grow">
            <a href="#" data-section="dashboard"
                class="nav-link flex items-center px-4 py-3 rounded-lg text-white hover:bg-white hover:bg-opacity-20 transition-all active">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293-.293a1 1 0 000-1.414l-7-7z">
                    </path>
                </svg>
                <span>Dasbor Admin</span>
            </a>

            <a href="#" data-section="requests"
                class="nav-link flex items-center px-4 py-3 rounded-lg text-white hover:bg-white hover:bg-opacity-20 transition-all">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M9 2a1 1 0 00-1 1v1H5a2 2 0 00-2 2v2a2 2 0 002 2h2.223a3 3 0 01.996-2h.858a3 3 0 01.996 2H15a2 2 0 002-2V6a2 2 0 00-2-2h-3V3a1 1 0 00-2 0v1h-3V3a1 1 0 00-1-1zm1 14a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd"></path>
                </svg>
                <span>Manajemen Permintaan</span>
            </a>

            <a href="#" data-section="billing"
                class="nav-link flex items-center px-4 py-3 rounded-lg text-white hover:bg-white hover:bg-opacity-20 transition-all">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v6a2 2 0 002 2h2v4l4-4h5a2 2 0 002-2V6a2 2 0 00-2-2H4z"></path>
                </svg>
                <span>Manajemen Pembayaran</span>
            </a>

            <a href="#" data-section="guidelines"
                class="nav-link flex items-center px-4 py-3 rounded-lg text-white hover:bg-white hover:bg-opacity-20 transition-all">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4 4a2 2 0 00-2 2v2a2 2 0 002 2h2v2H4a2 2 0 00-2 2v2a2 2 0 002 2h12a2 2 0 002-2v-2a2 2 0 00-2-2h-2v-2h2a2 2 0 002-2V6a2 2 0 00-2-2H4z"
                        clip-rule="evenodd"></path>
                </svg>
                <span>Manajemen Panduan</span>
            </a>

            <a href="#" data-section="users"
                class="nav-link flex items-center px-4 py-3 rounded-lg text-white hover:bg-white hover:bg-opacity-20 transition-all">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
                <span>Manajemen Pengguna</span>
            </a>

            <!-- TAMBAHKAN MENU PENGARSIPAN INI -->
            <a href="#" data-section="archive"
                class="nav-link flex items-center px-4 py-3 rounded-lg text-white hover:bg-white hover:bg-opacity-20 transition-all">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                    <path fill-rule="evenodd"
                        d="M3 8a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                <span>Pengarsipan</span>
            </a>
        </nav>

        <div class="mt-auto w-full">
            <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <main class="flex-1 p-8 main-content overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Dasbor Admin</h1>
            <div class="flex items-center space-x-4">
                <div class="text-gray-600">Selamat datang, Admin!</div>
                <div class="bg-indigo-600 text-white px-4 py-2 rounded-full font-semibold">admin</div>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Dashboard Section (Default) -->
        <div id="dashboard" class="section-content">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Permintaan Card -->
                <div class="card bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Permintaan</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ $totalSubmissions }}</p>
                            <p class="text-sm text-gray-500 mt-1">Permintaan data total.</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Permintaan Menunggu Card -->
                <div class="card bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Permintaan Menunggu</h3>
                            <p class="text-3xl font-bold text-yellow-600">{{ $pendingSubmissions }}</p>
                            <p class="text-sm text-gray-500 mt-1">Permintaan yang perlu diverifikasi.</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pembayaran Tertunda Card -->
                <div class="card bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Pembayaran Tertunda</h3>
                            <p class="text-3xl font-bold text-red-600">{{ $pendingPayments }}</p>
                            <p class="text-sm text-gray-500 mt-1">Pembayaran yang perlu dikonfirmasi.</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Perlu Verifikasi Card -->
                <div class="card bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Perlu Verifikasi</h3>
                            <p class="text-3xl font-bold text-orange-600">{{ $paidPayments ?? 0 }}</p>
                            <p class="text-sm text-gray-500 mt-1">Bukti pembayaran user perlu diverifikasi.</p>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-full">
                            <svg class="w-8 h-8 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Aksi Cepat</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <button onclick="showSection('requests')"
                        class="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg text-left transition-colors">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-500 text-white rounded-lg mr-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-1 1v1H5a2 2 0 00-2 2v2a2 2 0 002 2h2.223a3 3 0 01.996-2h.858a3 3 0 01.996 2H15a2 2 0 002-2V6a2 2 0 00-2-2h-3V3a1 1 0 00-2 0v1h-3V3a1 1 0 00-1-1zm1 14a1 1 0 100-2 1 1 0 000 2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Kelola Permintaan</h3>
                                <p class="text-sm text-gray-600">Verifikasi permintaan data</p>
                            </div>
                        </div>
                    </button>

                    <button onclick="showSection('billing')"
                        class="p-4 bg-green-50 hover:bg-green-100 rounded-lg text-left transition-colors">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-500 text-white rounded-lg mr-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v6a2 2 0 002 2h2v4l4-4h5a2 2 0 002-2V6a2 2 0 00-2-2H4z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Kelola Pembayaran</h3>
                                <p class="text-sm text-gray-600">Verifikasi pembayaran</p>
                            </div>
                        </div>
                    </button>

                    <button onclick="showSection('users')"
                        class="p-4 bg-purple-50 hover:bg-purple-100 rounded-lg text-left transition-colors">
                        <div class="flex items-center">
                            <div class="p-2 bg-purple-500 text-white rounded-lg mr-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Kelola Pengguna</h3>
                                <p class="text-sm text-gray-600">Manajemen user sistem</p>
                            </div>
                        </div>
                    </button>

                    <!-- TAMBAHKAN QUICK ACTION ARCHIVE INI -->
                    <button onclick="showSection('archive')"
                        class="p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg text-left transition-colors">
                        <div class="flex items-center">
                            <div class="p-2 bg-indigo-500 text-white rounded-lg mr-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                                    <path fill-rule="evenodd"
                                        d="M3 8a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Kelola Arsip</h3>
                                <p class="text-sm text-gray-600">Manajemen arsip data</p>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Aktivitas Terbaru</h2>
                <div class="space-y-4">
                    @forelse($submissions->take(5) as $submission)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-1 1v1H5a2 2 0 00-2 2v2a2 2 0 002 2h2.223a3 3 0 01.996-2h.858a3 3 0 01.996 2H15a2 2 0 002-2V6a2 2 0 00-2-2h-3V3a1 1 0 00-2 0v1h-3V3a1 1 0 00-1-1zm1 14a1 1 0 100-2 1 1 0 000 2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $submission->user->name }} mengajukan permintaan {{ $submission->data_type }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $submission->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex-shrink-0 space-x-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if ($submission->status === 'Diterima') bg-green-100 text-green-800
                                @elseif($submission->status === 'Diproses') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                    {{ $submission->status }}
                                </span>
                                @if ($submission->status === 'Diterima')
                                    <button onclick="archiveSubmission({{ $submission->id }})"
                                        class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded">
                                        Arsipkan
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada aktivitas terbaru</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Keep all existing sections (requests, billing, guidelines, users) as they are -->
        <!-- ... existing sections unchanged ... -->

        <!-- TAMBAHKAN SECTION PENGARSIPAN INI -->
        <!-- Pengarsipan Section -->
        <div id="archive" class="section-content hidden">
            <h2 class="text-2xl font-bold mb-6">Pengarsipan</h2>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="card bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Arsip</h3>
                            <p class="text-3xl font-bold text-purple-600">{{ $archivedSubmissions ?? 0 }}</p>
                            <p class="text-sm text-gray-500 mt-1">Dokumen yang diarsipkan</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-8 h-8 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                                <path fill-rule="evenodd"
                                    d="M3 8a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Berhasil Diselesaikan</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $completedArchives ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Pengajuan yang diterima</p>
                    </div>
                </div>

                <div class="card bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Ditolak</h3>
                        <p class="text-3xl font-bold text-red-600">{{ $rejectedArchives ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Pengajuan yang ditolak</p>
                    </div>
                </div>

                <div class="card bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Bulan Ini</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $monthlyArchives ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Arsip bulan ini</p>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Filter Data Arsip</h3>
                <form id="archiveFilterForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select id="filterYear"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Tahun</option>
                            @for ($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select id="filterMonth"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Bulan</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="filterStatus"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="Diterima">Diterima</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Data</label>
                        <select id="filterDataType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Jenis</option>
                            <option value="Data Iklim">Data Iklim</option>
                            <option value="Data Curah Hujan">Data Curah Hujan</option>
                            <option value="Data Angin">Data Angin</option>
                            <option value="Data Suhu">Data Suhu</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="button" onclick="filterArchiveData()"
                            class="flex-1 bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg">
                            Filter
                        </button>
                        <button type="button" onclick="exportArchiveData()"
                            class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                            Export
                        </button>
                    </div>
                </form>
            </div>

            <!-- Archive Table -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Data Pengarsipan</h3>
                    <div class="flex space-x-2">
                        <button onclick="refreshArchiveData()"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="archiveTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nomor Pengajuan</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pemohon</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jenis Data</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Arsip</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dokumen</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="archiveTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data akan dimuat via JavaScript -->
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex justify-center items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Memuat data arsip...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Keep all existing sections (requests, billing, guidelines, users) below -->
        <!-- ... paste the existing sections here unchanged ... -->

    </main>

    <!-- Keep all existing modals -->

    <script>
        // Enhanced Navigation System
        function showSection(sectionName) {
            // Hide all sections
            const sections = document.querySelectorAll('.section-content');
            sections.forEach(section => section.classList.add('hidden'));

            // Show selected section
            const targetSection = document.getElementById(sectionName);
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }

            // Update navigation active state
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.dataset.section === sectionName) {
                    link.classList.add('active');
                }
            });

            // Update page title based on section
            const titles = {
                'dashboard': 'Dasbor Admin',
                'requests': 'Manajemen Permintaan',
                'billing': 'Manajemen Pembayaran',
                'guidelines': 'Manajemen Panduan',
                'users': 'Manajemen Pengguna',
                'archive': 'Pengarsipan'
            };

            const pageTitle = titles[sectionName] || 'Dasbor Admin';
            document.querySelector('main h1').textContent = pageTitle;

            // Load archive data when archive section is shown
            if (sectionName === 'archive') {
                loadArchiveData();
            }
        }

        // Initialize navigation on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Add click listeners to navigation links
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionName = this.dataset.section;
                    showSection(sectionName);
                });
            });

            // Show dashboard by default
            showSection('dashboard');
        });

        // Archive Management Functions
        function filterArchiveData() {
            const year = document.getElementById('filterYear').value;
            const month = document.getElementById('filterMonth').value;
            const status = document.getElementById('filterStatus').value;
            const dataType = document.getElementById('filterDataType').value;

            const params = new URLSearchParams();
            if (year) params.append('year', year);
            if (month) params.append('month', month);
            if (status) params.append('status', status);
            if (dataType) params.append('data_type', dataType);

            loadArchiveData(params.toString());
        }

        function loadArchiveData(params = '') {
            const tableBody = document.getElementById('archiveTableBody');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        <div class="flex justify-center items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memuat data arsip...
                        </div>
                    </td>
                </tr>
            `;

            const url = `/admin/archive/api${params ? '?' + params : ''}`;

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    updateArchiveTable(data.archives);
                    updateArchiveStats(data.stats);
                })
                .catch(error => {
                    console.error('Error loading archive data:', error);
                    tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-red-500">
                            Error memuat data arsip. Silakan coba lagi.
                        </td>
                    </tr>
                `;
                });
        }

        function updateArchiveTable(archives) {
            const tableBody = document.getElementById('archiveTableBody');

            if (archives.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data arsip ditemukan.
                        </td>
                    </tr>
                `;
                return;
            }

            const rows = archives.map(archive => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${archive.submission_number}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${archive.user.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${archive.data_type}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadgeClass(archive.status)}">
                            ${archive.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDate(archive.archived_at)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex space-x-2">
                            ${archive.cover_letter_path ? `<a href="/admin/archive/${archive.id}/download/cover_letter" class="text-blue-600 hover:text-blue-900 text-xs">Surat</a>` : ''}
                            ${archive.pembayaran && archive.pembayaran.payment_proof_path ? `<a href="/admin/archive/${archive.id}/download/payment_proof" class="text-green-600 hover:text-green-900 text-xs">Bukti</a>` : ''}
                            ${archive.pembayaran && archive.pembayaran.billing_file_path ? `<a href="/admin/archive/${archive.id}/download/billing" class="text-yellow-600 hover:text-yellow-900 text-xs">Billing</a>` : ''}
                            ${archive.final_document_path ? `<a href="/admin/archive/${archive.id}/download/final_document" class="text-purple-600 hover:text-purple-900 text-xs">Final</a>` : ''}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button onclick="showArchiveDetail(${archive.id})" class="text-indigo-600 hover:text-indigo-900">Detail</button>
                        <button onclick="unarchiveSubmission(${archive.id})" class="text-red-600 hover:text-red-900">Unarchive</button>
                    </td>
                </tr>
            `).join('');

            tableBody.innerHTML = rows;
        }

        function getStatusBadgeClass(status) {
            switch (status) {
                case 'Diterima':
                    return 'bg-green-100 text-green-800';
                case 'Ditolak':
                    return 'bg-red-100 text-red-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID');
        }

        function refreshArchiveData() {
            loadArchiveData();
        }

        function exportArchiveData() {
            const year = document.getElementById('filterYear').value;
            const month = document.getElementById('filterMonth').value;
            const status = document.getElementById('filterStatus').value;
            const dataType = document.getElementById('filterDataType').value;

            const params = new URLSearchParams();
            if (year) params.append('year', year);
            if (month) params.append('month', month);
            if (status) params.append('status', status);
            if (dataType) params.append('data_type', dataType);

            window.location.href = `/admin/archive/export/data?${params.toString()}`;
        }

        function showArchiveDetail(id) {
            window.open(`/admin/archive/${id}`, '_blank');
        }

        function unarchiveSubmission(id) {
            if (confirm('Apakah Anda yakin ingin mengeluarkan data ini dari arsip?')) {
                fetch(`/admin/archive/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Data berhasil dikeluarkan dari arsip');
                            refreshArchiveData();
                        } else {
                            alert('Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan');
                    });
            }
        }

        function archiveSubmission(id) {
            if (confirm('Apakah Anda yakin ingin mengarsipkan pengajuan ini?')) {
                const notes = prompt('Catatan admin (opsional):') || '';

                fetch(`/admin/archive/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            admin_notes: notes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success || response.ok) {
                            alert('Pengajuan berhasil diarsipkan');
                            location.reload(); // Refresh dashboard
                        } else {
                            alert('Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Fallback to form submission
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/archive/${id}`;

                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const notesInput = document.createElement('input');
                        notesInput.type = 'hidden';
                        notesInput.name = 'admin_notes';
                        notesInput.value = notes;

                        form.appendChild(csrfInput);
                        form.appendChild(notesInput);
                        document.body.appendChild(form);
                        form.submit();
                    });
            }
        }

        function updateArchiveStats(stats) {
            // Update statistics if needed
            console.log('Archive stats:', stats);
        }

        // Keep all existing modal functions and other JavaScript unchanged
        // ... existing modal functions ...
    </script>
</body>

</html>
