<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_name',
        'description',
        'latitude',
        'longitude',
        'address_description',
        'severity',
        'aid_status',
        'aid_type_received',
        'verified_by_community_count',
    ];
}
