<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeedResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'need_id',
        'responder_name',
        'contact_info',
        'location',
        'items_offered',
        'photos',
        'secure_token',
    ];

    protected $casts = [
        'items_offered' => 'array',
        'photos' => 'array',
    ];

    public function need()
    {
        return $this->belongsTo(Need::class);
    }
}
