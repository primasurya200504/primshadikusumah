<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Arsip - {{ $archive->submission_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detail Arsip #{{ $archive->submission_number }}</h1>
                <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Tutup
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Informasi Pengajuan</h3>
                    <div class="space-y-2">
                        <p><strong>Nomor:</strong> {{ $archive->submission_number }}</p>
                        <p><strong>Pemohon:</strong> {{ $archive->user->name }}</p>
                        <p><strong>Email:</strong> {{ $archive->user->email }}</p>
                        <p><strong>Jenis Data:</strong> {{ $archive->data_type }}</p>
                        <p><strong>Status:</strong>
                            <span
                                class="px-2 py-1 rounded text-xs {{ $archive->status === 'Diterima' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $archive->status }}
                            </span>
                        </p>
                        <p><strong>Tanggal Arsip:</strong> {{ $archive->archived_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Dokumen</h3>
                    <div class="space-y-2">
                        @if ($archive->cover_letter_path)
                            <a href="{{ route('admin.archive.download', [$archive->id, 'cover_letter']) }}"
                                class="block text-blue-600 hover:text-blue-800">📄 Surat Pengantar</a>
                        @endif

                        @if ($archive->pembayaran && $archive->pembayaran->payment_proof_path)
                            <a href="{{ route('admin.archive.download', [$archive->id, 'payment_proof']) }}"
                                class="block text-green-600 hover:text-green-800">💳 Bukti Pembayaran</a>
                        @endif

                        @if ($archive->pembayaran && $archive->pembayaran->billing_file_path)
                            <a href="{{ route('admin.archive.download', [$archive->id, 'billing']) }}"
                                class="block text-yellow-600 hover:text-yellow-800">🧾 File Billing</a>
                        @endif

                        @if ($archive->final_document_path)
                            <a href="{{ route('admin.archive.download', [$archive->id, 'final_document']) }}"
                                class="block text-purple-600 hover:text-purple-800">📋 Dokumen Final</a>
                        @endif
                    </div>
                </div>
            </div>

            @if ($archive->admin_notes)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-2">Catatan Admin</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        {{ $archive->admin_notes }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
