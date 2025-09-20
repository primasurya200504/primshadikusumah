<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guideline;

class CleanGuidelinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua data contoh yang ada
        Guideline::truncate();

        // Buat data panduan yang bersih tanpa example_data
        $guidelines = [
            [
                'title' => 'Cara Mengajukan Permohonan Legalisir',
                'content' => 'Panduan lengkap untuk mengajukan permohonan legalisir dokumen di BMKG Pontianak.',
                'example_data' => null,
                'requirements' => json_encode([
                    'KTP/Identitas diri',
                    'Dokumen asli yang akan dilegalisir',
                    'Surat permohonan',
                    'Biaya administrasi'
                ])
            ],
            [
                'title' => 'Format Surat Permohonan',
                'content' => 'Template dan format yang benar untuk surat permohonan legalisir.',
                'example_data' => null,
                'requirements' => json_encode([
                    'Menggunakan kop surat resmi',
                    'Mencantumkan tujuan penggunaan',
                    'Dilengkapi dengan stempel basah'
                ])
            ],
            [
                'title' => 'Prosedur Pembayaran',
                'content' => 'Langkah-langkah pembayaran untuk proses legalisir dokumen.',
                'example_data' => null,
                'requirements' => json_encode([
                    'Transfer ke rekening resmi BMKG',
                    'Upload bukti pembayaran',
                    'Menunggu konfirmasi admin'
                ])
            ]
        ];

        foreach ($guidelines as $guideline) {
            Guideline::create($guideline);
        }

        $this->command->info('Guidelines data cleaned and reseeded successfully!');
    }
}
