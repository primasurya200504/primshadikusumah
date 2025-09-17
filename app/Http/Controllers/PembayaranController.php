<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;

class PembayaranController extends Controller
{
    public function formUpload($surat_id)
    {
        $submission = Submission::findOrFail($surat_id);
        return view('admin.pembayaran.upload', compact('submission'));
    }

    public function uploadBillink(Request $request, $surat_id)
    {
        $request->validate([
            'billink' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $submission = Submission::findOrFail($surat_id);

        if ($request->hasFile('billink')) {
            $file = $request->file('billink');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('billink', $filename, 'public');
            $submission->ebilling_path = $path;
            $submission->payment_status = 'Menunggu Pembayaran';
            $submission->save();
        }

        return redirect()->route('admin.pembayaran.upload', ['surat_id' => $surat_id])->with('success', 'E-billing berhasil diupload.');
    }

    public function index()
    {
        $submissions = Submission::where('status', 'Diterima')
            ->orWhere('payment_status', 'Menunggu Pembayaran')
            ->with('user')
            ->get();

        return view('admin.pembayaran.index', compact('submissions'));
    }

    // User melihat billing dan upload bukti pembayaran
    public function showUser($surat_id)
    {
        $submission = Submission::with('user')->findOrFail($surat_id);
        return view('user.pembayaran.show', compact('submission'));
    }

    public function uploadBukti(Request $request, $surat_id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $submission = Submission::findOrFail($surat_id);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
            $submission->payment_proof_path = $path;
            $submission->payment_status = 'Menunggu Verifikasi Pembayaran';
            $submission->save();
        }

        return redirect()->route('user.pembayaran.show', ['surat_id' => $surat_id])->with('success', 'Bukti pembayaran berhasil diupload.');
    }

    // Admin melihat bukti pembayaran user
    public function showAdmin($surat_id)
    {
        $submission = Submission::with('user')->findOrFail($surat_id);
        return view('admin.pembayaran.detail', compact('submission'));
    }
}
