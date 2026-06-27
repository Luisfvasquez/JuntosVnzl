<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Volunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'dni',
        'phone',
        'email',
        'skill_category',
        'availability_description',
        'modality',
        'location_description',
        'latitude',
        'longitude',
        'description',
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
