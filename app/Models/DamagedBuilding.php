<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DamagedBuilding extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'zone',
        'latitude',
        'longitude',
        'damage_level',
        'status',
        'general_source',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->secure_token)) {
                $model->secure_token = (string) Str::uuid();
            }
        });
    }
}
