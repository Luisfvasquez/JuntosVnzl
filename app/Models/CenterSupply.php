<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterSupply extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_center_id',
        'supply_name',
        'quantity',
        'unit_of_measurement',
        'priority_level',
    ];

    public function center()
    {
        return $this->belongsTo(CollectionCenter::class, 'collection_center_id');
    }
}
