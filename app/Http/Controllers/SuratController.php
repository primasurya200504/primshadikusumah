<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuratController extends Controller
{
    /**
     * Simpan surat yang diunggah.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadSurat(Request $request)
    {
        // Cek apakah file 'surat_pengantar' ada
        if ($request->hasFile('surat_pengantar')) {
            // Simpan file ke direktori 'surat_pengantar' di dalam 'storage/app/public'
            $path = $request->file('surat_pengantar')->store('surat_pengantar', 'public');

            // Simpan path ke database jika diperlukan
            // $user->surat_pengantar_path = $path;
            // $user->save();

            return redirect()->back()->with('success', 'Surat berhasil diunggah!');
        }

        return redirect()->back()->with('error', 'Tidak ada file yang diunggah.');
    }
}
