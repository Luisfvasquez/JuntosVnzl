<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AidOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider_name',
        'dni',
        'phone',
        'title',
        'description',
        'category',
        'latitude',
        'longitude',
        'address_description',
        'status',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
