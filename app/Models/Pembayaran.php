<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'submission_id',
        'billing_file_path',
        'billing_filename',
        'billing_amount',
        'billing_note',
        'billing_date',
        'payment_proof_path',
        'payment_proof_filename',
        'payment_date',
        'payment_note',
        'status',
        'uploaded_by',
        'uploaded_at',
        'verified_by',
        'verified_at'
    ];

    protected $casts = [
        'billing_date' => 'date',
        'payment_date' => 'date',
        'uploaded_at' => 'datetime',
        'verified_at' => 'datetime'
    ];

    // Relasi ke Submission
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    // Relasi ke User (yang upload)
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Relasi ke User (yang verifikasi)
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
