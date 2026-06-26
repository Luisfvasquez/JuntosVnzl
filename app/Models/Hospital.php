<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'latitude',
        'longitude',
        'address_description',
        'status',
        'contact_phone',
        'image_url',
    ];

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}
