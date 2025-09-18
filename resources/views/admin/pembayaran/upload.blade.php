<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Billing - BMKG Pontianak</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .upload-area {
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: #6366f1;
            background-color: #f8fafc;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h1 class="text-2xl font-bold text-white">Upload Billing</h1>
                    <p class="text-indigo-100">Pengajuan #{{ $submission->nomor_pengajuan ?? $surat_id }}</p>
                    <p class="text-indigo-100">Pemohon: {{ $submission->user->name ?? 'User Baru' }}</p>
                </div>

                <div class="p-6">
                    <!-- Alert Messages -->
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Upload Form -->
                    <form action="{{ route('admin.pembayaran.upload.store', $surat_id) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- File Upload - DIPERBAIKI -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                File Billing *
                            </label>

                            <!-- File Input (Hidden) -->
                            <input type="file" id="billing_file" name="billing_file"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden" required>

                            <!-- Upload Area (Clickable) -->
                            <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-500 transition-colors"
                                onclick="document.getElementById('billing_file').click()">

                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                                <p class="text-gray-600 text-lg font-medium">
                                    <span class="text-indigo-600 hover:text-indigo-500">Upload file</span> atau drag and
                                    drop
                                </p>
                                <p class="text-gray-500 text-sm mt-2">PDF, PNG, JPG, DOC hingga 5MB</p>

                                <!-- File Name Display -->
                                <div id="file-info" class="mt-4 hidden">
                                    <p class="text-sm text-green-600 font-medium" id="file-name"></p>
                                    <p class="text-xs text-gray-500" id="file-size"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Jumlah Tagihan -->
                        <div>
                            <label for="jumlah_tagihan" class="block text-sm font-semibold text-gray-700 mb-2">
                                Jumlah Tagihan (Rp)
                            </label>
                            <input type="number" id="jumlah_tagihan" name="jumlah_tagihan"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Masukkan jumlah tagihan" min="0">
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                                Keterangan
                            </label>
                            <textarea id="keterangan" name="keterangan" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Tambahkan keterangan billing (opsional)"></textarea>
                        </div>

                        <!-- Tanggal Billing -->
                        <div>
                            <label for="tanggal_billing" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Billing
                            </label>
                            <input type="date" id="tanggal_billing" name="tanggal_billing"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Buttons -->
                        <div class="flex space-x-4 pt-4">
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold py-3 px-6 rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all duration-200">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    Upload Billing
                                </span>
                            </button>
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex-1 bg-gray-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-600 transition-all duration-200 text-center flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // File upload handling - JAVASCRIPT DIPERBAIKI
        const fileInput = document.getElementById('billing_file');
        const uploadArea = document.querySelector('.upload-area');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');

        // Handle file selection
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File terlalu besar! Maksimal 5MB');
                    this.value = '';
                    return;
                }

                // Display file info
                fileName.textContent = file.name;
                fileSize.textContent = `Ukuran: ${(file.size / 1024 / 1024).toFixed(2)} MB`;
                fileInfo.classList.remove('hidden');

                // Change upload area appearance
                uploadArea.classList.add('border-green-500', 'bg-green-50');
                uploadArea.classList.remove('border-gray-300');

                console.log(`File selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`);
            }
        });

        // Handle drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-indigo-500', 'bg-indigo-50');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-500', 'bg-indigo-50');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-500', 'bg-indigo-50');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>

</html>
