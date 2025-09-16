<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;

class PembayaranController extends Controller
{
    public function formUpload($surat_id)
    {
        $pembayaran = Pembayaran::where('surat_id', $surat_id)->firstOrFail();
        return view('admin.pembayaran.upload', compact('pembayaran'));
    }

    public function uploadBillink(Request $request, $surat_id)
    {
        $request->validate([
            'billink' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);
        $pembayaran = Pembayaran::where('surat_id', $surat_id)->firstOrFail();
        if ($request->hasFile('billink')) {
            $file = $request->file('billink');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('billink', $filename, 'public');
            $pembayaran->billink = $path;
            $pembayaran->save();
        }
        return redirect()->route('admin.pembayaran.upload', ['surat_id' => $surat_id])->with('success', 'Billink berhasil diupload.');
    }

    public function index()
    {
        $pembayaran = \App\Models\Pembayaran::whereHas('surat', function ($q) {
            $q->where('status', 'Diterima');
        })->get();

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    // User melihat billing dan upload bukti pembayaran
    public function showUser($surat_id)
    {
        $pembayaran = Pembayaran::where('surat_id', $surat_id)->firstOrFail();
        return view('user.pembayaran.show', compact('pembayaran'));
    }

    public function uploadBukti(Request $request, $surat_id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);
        $pembayaran = Pembayaran::where('surat_id', $surat_id)->firstOrFail();
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
            $pembayaran->bukti_pembayaran = $path;
            $pembayaran->save();
        }
        return redirect()->route('user.pembayaran.show', ['surat_id' => $surat_id])->with('success', 'Bukti pembayaran berhasil diupload.');
    }

    // Admin melihat bukti pembayaran user
    public function showAdmin($surat_id)
    {
        $pembayaran = Pembayaran::where('surat_id', $surat_id)->firstOrFail();
        return view('admin.pembayaran.detail', compact('pembayaran'));
    }
}
