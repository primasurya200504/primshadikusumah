<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'user_id', 
        'submission_number',
        'data_type',
        'status',
        'admin_notes',
        'cover_letter_path',
        'final_document_path',
        'archived_at'
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'submission_id', 'submission_id');
    }
}
