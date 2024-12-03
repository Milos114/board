<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'file_hash',
        'uploaded_by',
        'file_extension'
    ];

    protected $table = 'attachments';
}
