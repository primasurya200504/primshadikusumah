<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        $rules = [
            'jenis_data' => 'required|string|max:255',
            'kategori' => 'required|string|in:PNBP,Non-PNBP',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keperluan' => 'required|string|max:1000',
        ];

        // Validasi file sesuai kategori
        if ($this->input('kategori') === 'PNBP') {
            $rules['files'] = 'required|array|min:1|max:1';
        } else {
            $rules['files'] = 'required|array|min:1|max:4';
        }

        $rules['files.*'] = 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240';

        return $rules;
    }

    public function messages()
    {
        return [
            'jenis_data.required' => 'Jenis data wajib dipilih.',
            'kategori.required' => 'Kategori pengajuan wajib dipilih.',
            'keperluan.required' => 'Keperluan penggunaan data wajib diisi.',
            'files.required' => 'File surat pengantar wajib diupload.',
            'files.*.mimes' => 'Format file tidak didukung. Gunakan PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
            'files.*.max' => 'Ukuran file maksimal 10MB.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ];
    }
}
