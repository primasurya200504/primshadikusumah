<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guideline;

class GuidelineSeeder extends Seeder
{
    public function run()
    {
        $guidelines = [
            [
                'title' => 'Cara Mengajukan Permohonan Legalisir',
                'content' => 'Panduan lengkap untuk mengajukan permohonan legalisir dokumen di BMKG.',
                'example_data' => json_encode([
                    'dokumen_diperlukan' => ['KTP', 'Ijazah', 'Transkrip'],
                    'waktu_proses' => '3-5 hari kerja',
                    'biaya' => 'Rp 50.000'
                ]),
                'requirements' => json_encode([
                    'Fotokopi KTP yang masih berlaku',
                    'Dokumen asli yang akan dilegalisir',
                    'Surat permohonan',
                    'Bukti pembayaran'
                ])
            ],
            [
                'title' => 'Format Surat Permohonan',
                'content' => 'Template dan format yang benar untuk surat permohonan legalisir.',
                'example_data' => json_encode([
                    'format' => 'PDF atau DOC',
                    'ukuran_maksimal' => '5MB',
                    'template' => 'Tersedia di website'
                ]),
                'requirements' => json_encode([
                    'Menggunakan kop surat resmi',
                    'Mencantumkan tujuan legalisir',
                    'Ditandatangani pemohon',
                    'Bermaterai 6000'
                ])
            ],
            [
                'title' => 'Prosedur Pembayaran',
                'content' => 'Panduan pembayaran untuk layanan legalisir dokumen.',
                'example_data' => json_encode([
                    'metode_pembayaran' => ['Transfer Bank', 'Tunai'],
                    'rekening' => 'BNI 1234567890',
                    'atas_nama' => 'BMKG Pontianak'
                ]),
                'requirements' => json_encode([
                    'Upload bukti pembayaran',
                    'Pembayaran sesuai tarif',
                    'Konfirmasi via WhatsApp',
                    'Simpan bukti transfer'
                ])
            ]
        ];

        foreach ($guidelines as $guideline) {
            Guideline::create($guideline);
        }
    }
}
