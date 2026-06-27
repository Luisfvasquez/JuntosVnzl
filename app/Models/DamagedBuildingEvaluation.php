<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamagedBuildingEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'damaged_building_id',
        'evaluator_name',
        'evaluator_credential',
        'structural_damage',
        'habitability_status',
        'collapse_risk',
        'recommended_actions',
        'comments',
        'is_verifiable',
        'is_verified',
        'image_urls',
    ];

    protected $casts = [
        'is_verifiable' => 'boolean',
        'is_verified' => 'boolean',
        'image_urls' => 'array',
    ];

    public function damagedBuilding()
    {
        return $this->belongsTo(DamagedBuilding::class);
    }
}
