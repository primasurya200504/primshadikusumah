<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionFile extends Model
{
    protected $fillable = [
        'submission_id',
        'file_name',
        'file_path',
        'file_type'
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
