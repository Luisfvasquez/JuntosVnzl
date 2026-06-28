<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id',
        'hospital_area',
        'dni',
        'first_name',
        'last_name',
        'gender',
        'approximate_age',
        'physical_description',
        'medical_status',
        'arrival_date',
        'photo_url',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
