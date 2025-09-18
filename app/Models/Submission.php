<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'submission_number',
        'data_type',
        'category',
        'start_date',
        'end_date',
        'purpose',
        'status',
        'payment_status',
        'cover_letter_path',
        'rejection_note',
        'is_archived',
        'archived_at',
        'final_document_path',
        'admin_notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'archived_at' => 'datetime',
        'is_archived' => 'boolean'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke SubmissionFile
    public function files()
    {
        return $this->hasMany(SubmissionFile::class);
    }

    // Relasi ke Pembayaran
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk data yang diarsipkan
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    // Scope untuk data yang belum diarsipkan
    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }
}
