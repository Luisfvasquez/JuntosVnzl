<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AffectedZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'severity',
        'description',
        'latitude',
        'longitude',
        'status',
        'is_attended',
        'secure_token',
        'audit_name',
        'audit_phone',
    ];

    protected $casts = [
        'is_attended' => 'boolean',
    ];

    public function updates()
    {
        return $this->hasMany(AffectedZoneUpdate::class);
    }

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
