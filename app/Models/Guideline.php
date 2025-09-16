<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guideline extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'example_data',
        'requirements',
    ];

    protected $casts = [
        'example_data' => 'array',
        'requirements' => 'array',
    ];
}
