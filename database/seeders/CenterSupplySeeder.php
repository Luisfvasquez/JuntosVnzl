<?php

namespace Database\Seeders;

use App\Models\CenterSupply;
use App\Models\CollectionCenter;
use Illuminate\Database\Seeder;

class CenterSupplySeeder extends Seeder
{
    public function run(): void
    {
        /*   $centers = CollectionCenter::all();

          foreach ($centers as $center) {
              if (str_contains($center->center_name, 'Cruz Roja')) {
                  // Insumos médicos críticos
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Kits de Primeros Auxilios',
                      'quantity' => 150.00,
                      'unit_of_measurement' => 'unidades',
                      'priority_level' => 'critico',
                  ]);
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Gasas Estériles y Vendas',
                      'quantity' => 500.00,
                      'unit_of_measurement' => 'unidades',
                      'priority_level' => 'requerido',
                  ]);
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Fisiológico en Solución (Suero)',
                      'quantity' => 300.00,
                      'unit_of_measurement' => 'litros',
                      'priority_level' => 'critico',
                  ]);
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Guantes de látex quirúrgicos',
                      'quantity' => 1200.00,
                      'unit_of_measurement' => 'unidades',
                      'priority_level' => 'estable',
                  ]);
              }

              if (str_contains($center->center_name, 'Chiquinquirá')) {
                  // Alimentos y ropa
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Agua Mineral Potable',
                      'quantity' => 1200.00,
                      'unit_of_measurement' => 'litros',
                      'priority_level' => 'critico',
                  ]);
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Arroz y Harina Pan',
                      'quantity' => 450.00,
                      'unit_of_measurement' => 'kg',
                      'priority_level' => 'requerido',
                  ]);
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Alimentos enlatados (Atún/Sardina)',
                      'quantity' => 600.00,
                      'unit_of_measurement' => 'unidades',
                      'priority_level' => 'estable',
                  ]);
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Ropa de niños (variados)',
                      'quantity' => 15.00,
                      'unit_of_measurement' => 'cajas',
                      'priority_level' => 'estable',
                  ]);
              }

              if (str_contains($center->center_name, 'Polideportivo')) {
                  // Herramientas y logística
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Manta térmica e impermeables',
                      'quantity' => 400.00,
                      'unit_of_measurement' => 'unidades',
                      'priority_level' => 'critico',
                  ]);
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Pilas y Linternas LED',
                      'quantity' => 250.00,
                      'unit_of_measurement' => 'unidades',
                      'priority_level' => 'requerido',
                  ]);
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Palas y Cascos de Rescate',
                      'quantity' => 80.00,
                      'unit_of_measurement' => 'unidades',
                      'priority_level' => 'estable',
                  ]);
              }

              if (str_contains($center->center_name, 'Gimnasio')) {
                  // Alimentos excedentes
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Agua Emboteallada',
                      'quantity' => 5000.00,
                      'unit_of_measurement' => 'litros',
                      'priority_level' => 'excedente',
                  ]);
                  CenterSupply::create([
                      'collection_center_id' => $center->id,
                      'supply_name' => 'Colchonetas Inflables',
                      'quantity' => 120.00,
                      'unit_of_measurement' => 'unidades',
                      'priority_level' => 'requerido',
                  ]);
              }
          } */
    }
}
