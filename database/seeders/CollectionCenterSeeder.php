<?php

namespace Database\Seeders;

use App\Models\CollectionCenter;
use App\Models\User;
use Illuminate\Database\Seeder;

class CollectionCenterSeeder extends Seeder
{
    public function run(): void
    {
        $coordinator = User::where('role', 'coordinator')->first();
        $coordinatorId = $coordinator ? $coordinator->id : null;

        CollectionCenter::create([
            'user_id' => $coordinatorId,
            'center_name' => 'Sede Cruz Roja Venezolana - Caracas',
            'manager_name' => 'Dr. José Moreno',
            'dni' => 'V-9123456',
            'phone' => '+584126667788',
            'latitude' => 10.50420000,
            'longitude' => -66.90150000,
            'address_description' => 'Avenida Andrés Bello, Edificio Cruz Roja, La Candelaria.',
            'status' => 'recibiendo_insumos',
        ]);

        CollectionCenter::create([
            'user_id' => null, // Creado anónimamente en Fase I
            'center_name' => 'Iglesia La Chiquinquirá (Parroquia)',
            'manager_name' => 'Padre Luis Rivas',
            'dni' => 'V-6154321',
            'phone' => '+584143332211',
            'latitude' => 10.49520000,
            'longitude' => -66.87980000,
            'address_description' => 'Avenida Andrés Bello, frente a la Plaza Andrés Bello, La Florida.',
            'status' => 'recibiendo_insumos',
        ]);

        CollectionCenter::create([
            'user_id' => null,
            'center_name' => 'Polideportivo Chacao - Centro Logístico',
            'manager_name' => 'Sofía Escalona',
            'dni' => 'V-19876543',
            'phone' => '+584245558899',
            'latitude' => 10.49050000,
            'longitude' => -66.85230000,
            'address_description' => 'Avenida Francisco de Miranda, entrada por las canchas de tenis.',
            'status' => 'distribuyendo',
        ]);

        CollectionCenter::create([
            'user_id' => null,
            'center_name' => 'Gimnasio Papá Carrillo (Parque Miranda)',
            'manager_name' => 'Roberto Vallenilla',
            'dni' => 'V-10234567',
            'phone' => '+584167771122',
            'latitude' => 10.49120000,
            'longitude' => -66.83780000,
            'address_description' => 'Avenida Francisco de Miranda, Los Dos Caminos. Instalaciones del domo.',
            'status' => 'capacidad_maxima',
        ]);
    }
}
