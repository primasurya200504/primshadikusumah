<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class AdminSuratController extends Controller
{
    // ...existing methods...

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $surat = Surat::findOrFail($id);
        $surat->status = $request->status;
        $surat->save();

        if ($request->status == 'Diterima') {
            \App\Models\Pembayaran::firstOrCreate([
                'surat_id' => $surat->id,
            ]);
            // Redirect ke halaman manajemen pembayaran
            return redirect()->route('admin.pembayaran.index')->with('success', 'Status surat diperbarui dan data masuk ke manajemen pembayaran.');
        }

        return redirect()->back()->with('success', 'Status surat diperbarui.');
    }

    // ...existing methods...
}
