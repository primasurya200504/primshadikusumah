<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Bukti Pembayaran - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                    <h1 class="text-2xl font-bold text-white">Detail Bukti Pembayaran</h1>
                    <p class="text-blue-100">Pengajuan: {{ $pembayaran->submission->submission_number }}</p>
                </div>

                <div class="p-6">
                    <!-- Info Pengajuan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-3">📋 Informasi Pengajuan</h3>
                            <div class="space-y-2">
                                <div>
                                    <span class="text-sm text-gray-600">Nomor:</span>
                                    <span class="font-medium">{{ $pembayaran->submission->submission_number }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Pemohon:</span>
                                    <span class="font-medium">{{ $pembayaran->submission->user->name }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Jenis Data:</span>
                                    <span class="font-medium">{{ $pembayaran->submission->data_type }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-800 mb-3">💰 Informasi Billing</h3>
                            <div class="space-y-2">
                                <div>
                                    <span class="text-sm text-gray-600">Jumlah Tagihan:</span>
                                    <span class="font-bold text-lg">Rp {{ number_format($pembayaran->billing_amount ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Tanggal Billing:</span>
                                    <span class="font-medium">{{ $pembayaran->billing_date ? $pembayaran->billing_date->format('d F Y') : '-' }}</span>
                                </div>
                                @if($pembayaran->billing_note)
                                <div>
                                    <span class="text-sm text-gray-600">Keterangan:</span>
                                    <span class="font-medium">{{ $pembayaran->billing_note }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Bukti Pembayaran -->
                    @if($pembayaran->payment_proof_path)
                    <div class="bg-green-50 p-6 rounded-lg mb-6">
                        <h3 class="font-semibold text-green-800 mb-4">📸 Bukti Pembayaran dari User</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm text-gray-600">Tanggal Pembayaran:</span>
                                        <span class="font-medium">{{ $pembayaran->payment_date ? $pembayaran->payment_date->format('d F Y') : '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Catatan User:</span>
                                        <span class="font-medium">{{ $pembayaran->payment_note ?? 'Tidak ada catatan' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Status:</span>
                                        <span class="font-bold text-green-600">{{ $pembayaran->status }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                    @php
                                        $fileExtension = pathinfo($pembayaran->payment_proof_path, PATHINFO_EXTENSION);
                                    @endphp
                                    
                                    @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']))
                                        <img src="{{ asset('storage/' . $pembayaran->payment_proof_path) }}" 
                                             alt="Bukti Pembayaran" 
                                             class="max-w-full h-auto rounded-lg shadow-lg">
                                    @else
                                        <div class="py-8">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-gray-500">File PDF</p>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mt-4 text-center">
                                    <a href="{{ asset('storage/' . $pembayaran->payment_proof_path) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download Bukti
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Form Verifikasi -->
                    <form action="{{ route('admin.pembayaran.status.update', $pembayaran->id) }}" method="POST" class="bg-white border border-gray-200 p-6 rounded-lg">
                        @csrf
                        @method('PATCH')
                        
                        <h3 class="font-semibold text-gray-800 mb-4">🔄 Update Status Pembayaran</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                                <select name="status" class="w-full border-gray-300 rounded-lg">
                                    <option value="Dibayar" {{ $pembayaran->status === 'Dibayar' ? 'selected' : '' }}>Dibayar (Menunggu Verifikasi)</option>
                                    <option value="Terverifikasi" {{ $pembayaran->status === 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                                    <option value="Selesai" {{ $pembayaran->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="Ditolak" {{ $pembayaran->status === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan (Opsional)</label>
                            <textarea name="keterangan_status" rows="3" 
                                      class="w-full border-gray-300 rounded-lg"
                                      placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                        </div>
                        
                        <div class="flex justify-between">
                            <a href="{{ route('admin.dashboard') }}" 
                               class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                ← Kembali ke Dashboard
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                💾 Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
