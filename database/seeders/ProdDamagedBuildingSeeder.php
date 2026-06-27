<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DamagedBuilding;
use Illuminate\Support\Facades\File;

class ProdDamagedBuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(base_path('edificios_verificados.json'));
        $buildings = json_decode($json, true);

        foreach ($buildings as $buildingData) {
            DamagedBuilding::updateOrCreate(
                [
                    'name' => mb_substr($buildingData['name'], 0, 255)
                ],
                [
                    'city' => isset($buildingData['city']) ? mb_substr($buildingData['city'], 0, 150) : null,
                    'zone' => isset($buildingData['zone']) ? mb_substr($buildingData['zone'], 0, 150) : null,
                    'latitude' => $buildingData['lat'] ?? null,
                    'longitude' => $buildingData['lng'] ?? null,
                    'damage_level' => isset($buildingData['damage_level']) ? mb_substr($buildingData['damage_level'], 0, 50) : null,
                    'status' => isset($buildingData['status']) ? mb_substr($buildingData['status'], 0, 50) : 'reportado',
                    'general_source' => isset($buildingData['general_source']) ? mb_substr($buildingData['general_source'], 0, 255) : null,
                    'notes' => $buildingData['notes'] ?? null,
                ]
            );
        }
    }
}
