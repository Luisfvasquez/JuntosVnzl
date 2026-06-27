<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Need extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_name',
        'phone',
        'items_list',
        'description',
        'location',
        'latitude',
        'longitude',
        'photos',
        'status',
        'secure_token',
    ];

    protected $casts = [
        'items_list' => 'array',
        'photos' => 'array',
    ];

    public function responses()
    {
        return $this->hasMany(NeedResponse::class);
    }
}
