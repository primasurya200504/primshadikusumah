<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Pembayaran - BMKG Pontianak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                    <h1 class="text-2xl font-bold text-white">Upload Bukti Pembayaran</h1>
                    <p class="text-blue-100">Pengajuan: {{ $submission->submission_number }}</p>
                    <p class="text-blue-100">Jenis Data: {{ $submission->data_type }}</p>
                </div>

                <div class="p-6">
                    <!-- Info Billing -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-blue-800 mb-3">📋 Informasi Billing:</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Jumlah Tagihan:</p>
                                <p class="font-bold text-lg">Rp
                                    {{ number_format($submission->pembayaran->billing_amount ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Tanggal Billing:</p>
                                <p class="font-semibold">
                                    {{ $submission->pembayaran->billing_date ? $submission->pembayaran->billing_date->format('d F Y') : '-' }}
                                </p>
                            </div>
                        </div>
                        @if ($submission->pembayaran->billing_note)
                            <div class="mt-3">
                                <p class="text-sm text-gray-600">Keterangan:</p>
                                <p class="text-gray-800">{{ $submission->pembayaran->billing_note }}</p>
                            </div>
                        @endif
                    </div>

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
                    <form action="{{ route('user.pembayaran.upload.store', $submission->id) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- File Upload -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Bukti Pembayaran *
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors cursor-pointer"
                                onclick="document.getElementById('payment_proof').click()">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="text-gray-600 text-lg font-medium mb-2">
                                    Pilih file bukti pembayaran
                                </p>
                                <p class="text-gray-500 text-sm">PDF, JPG, PNG hingga 5MB</p>

                                <input type="file" id="payment_proof" name="payment_proof"
                                    accept=".pdf,.jpg,.jpeg,.png" class="hidden" required>

                                <div id="file-info" class="mt-4 hidden">
                                    <p class="text-sm text-green-600 font-medium" id="file-name"></p>
                                    <p class="text-xs text-gray-500" id="file-size"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Pembayaran -->
                        <div>
                            <label for="payment_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Pembayaran *
                            </label>
                            <input type="date" id="payment_date" name="payment_date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label for="payment_note" class="block text-sm font-semibold text-gray-700 mb-2">
                                Catatan (Opsional)
                            </label>
                            <textarea id="payment_note" name="payment_note" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Tambahkan catatan tentang pembayaran (misal: metode pembayaran, nomor referensi, dll.)"></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-3 px-6 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-200">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    Upload Bukti Pembayaran
                                </span>
                            </button>
                            <a href="{{ route('user.dashboard') }}"
                                class="flex-1 bg-gray-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-600 text-center flex items-center justify-center transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali ke Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h3 class="font-semibold text-gray-800 mb-4">📝 Petunjuk Upload Bukti Pembayaran:</h3>
                <ul class="list-disc list-inside space-y-2 text-gray-600 text-sm">
                    <li>File bukti pembayaran harus berupa gambar (JPG, PNG) atau PDF dengan ukuran maksimal 5MB</li>
                    <li>Pastikan bukti pembayaran mencantumkan nominal yang sesuai dengan billing</li>
                    <li>Tanggal pembayaran tidak boleh melebihi tanggal hari ini</li>
                    <li>Setelah upload berhasil, admin akan memverifikasi bukti pembayaran Anda</li>
                    <li>Status pembayaran akan diupdate setelah verifikasi admin selesai</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // File upload preview
        const fileInput = document.getElementById('payment_proof');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('File terlalu besar! Maksimal 5MB');
                    this.value = '';
                    fileInfo.classList.add('hidden');
                    return;
                }

                fileName.textContent = file.name;
                fileSize.textContent = `Ukuran: ${(file.size / 1024 / 1024).toFixed(2)} MB`;
                fileInfo.classList.remove('hidden');
            } else {
                fileInfo.classList.add('hidden');
            }
        });
    </script>
</body>

</html>
