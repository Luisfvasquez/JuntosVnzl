<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LostPet extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_name',
        'contact_phone',
        'pet_name',
        'species',
        'breed',
        'description',
        'latitude',
        'longitude',
        'address_description',
        'status',
        'photo_url',
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
