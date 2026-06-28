<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AffectedZoneUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'affected_zone_id',
        'user_id',
        'author_name',
        'message',
        'is_attended_change',
        'status_change',
        'image_urls',
        'secure_token',
    ];

    protected $casts = [
        'is_attended_change' => 'boolean',
        'image_urls' => 'array',
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

    public function zone()
    {
        return $this->belongsTo(AffectedZone::class, 'affected_zone_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
