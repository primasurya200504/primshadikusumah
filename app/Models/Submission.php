<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import class HasMany

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_number',
        'user_id',
        'submission_type', // Menambahkan kolom baru untuk PNBP/Non-PNBP
        'data_type',
        'category',
        'start_date',
        'end_date',
        'purpose',
        'cover_letter_path',
        'status',
        'payment_status',
        'rejection_note',
        'ebilling_path',
        'payment_proof_path',
    ];

    /**
     * Dapatkan user yang memiliki pengajuan ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Dapatkan file-file yang terkait dengan pengajuan ini.
     */
    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }
}
