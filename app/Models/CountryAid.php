<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryAid extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_name',
        'iso_code',
        'aid_type',
        'description',
        'status',
    ];
}
