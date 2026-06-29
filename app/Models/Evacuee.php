<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evacuee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'dni',
        'age',
        'gender',
        'photo_url',
        'current_location',
        'latitude',
        'longitude',
        'origin_address',
        'special_conditions',
        'minor_medical_conditions',
        'immediate_needs',
        'dependents_count',
        'looking_for',
        'head_of_family_id',
        'family_relationship',
        'status',
    ];

    public function headOfFamily()
    {
        return $this->belongsTo(Evacuee::class, 'head_of_family_id');
    }

    public function familyMembers()
    {
        return $this->hasMany(Evacuee::class, 'head_of_family_id');
    }
}
