<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CollectionCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'center_name',
        'manager_name',
        'dni',
        'phone',
        'latitude',
        'longitude',
        'address_description',
        'status',
        'secure_token',
        'audit_name',
        'audit_phone',
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

    public function supplies()
    {
        return $this->hasMany(CenterSupply::class);
    }
}
