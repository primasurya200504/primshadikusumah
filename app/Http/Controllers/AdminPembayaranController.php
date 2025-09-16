<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminPembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = \App\Models\Pembayaran::whereHas('surat', function ($q) {
            $q->where('status', 'Diterima');
        })->get();

        return view('admin.pembayaran.index', compact('pembayaran'));
    }
}
