<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hospital;

class HospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitals = [
            [
                'name' => 'Hospital Universitario de Caracas',
                'type' => 'hospital_publico',
                'latitude' => 10.489115,
                'longitude' => -66.890666,
                'address_description' => 'Ciudad Universitaria de Caracas, UCV.',
                'status' => 'operativo',
            ],
            [
                'name' => 'Cruz Roja (Caracas)',
                'type' => 'clinica_privada',
                'latitude' => 10.505711,
                'longitude' => -66.901511,
                'address_description' => 'Av. Andrés Bello, San Bernardino, Caracas.',
                'status' => 'operativo',
            ],
            [
                'name' => 'Hospital Ricardo Baquero González (Periférico de Catia)',
                'type' => 'hospital_publico',
                'latitude' => 10.509743,
                'longitude' => -66.948270,
                'address_description' => 'Calle Nueva Esparta, Catia, Caracas.',
                'status' => 'operativo',
            ],
            [
                'name' => 'Hospital Dr. Domingo Luciani (El Llanito)',
                'type' => 'hospital_publico',
                'latitude' => 10.473551,
                'longitude' => -66.811652,
                'address_description' => 'Avenida Principal del Llanito, Petare.',
                'status' => 'operativo',
            ],
            [
                'name' => 'Hospital Dr. Miguel Pérez Carreño',
                'type' => 'hospital_publico',
                'latitude' => 10.467471,
                'longitude' => -66.944322,
                'address_description' => 'Urb. La Yaguara, Parroquia El Paraíso, Caracas.',
                'status' => 'operativo',
            ],
            [
                'name' => 'Hospital JM de los Ríos (Hospital de Niños)',
                'type' => 'hospital_publico',
                'latitude' => 10.508500,
                'longitude' => -66.902300,
                'address_description' => 'Av. Vollmer, San Bernardino, Caracas.',
                'status' => 'operativo',
            ],
            [
                'name' => 'Hospital Vargas de Caracas',
                'type' => 'hospital_publico',
                'latitude' => 10.514032,
                'longitude' => -66.912643,
                'address_description' => 'San José, Caracas.',
                'status' => 'operativo',
            ],
            [
                'name' => 'Hospital Dr. José María Vargas (La Guaira)',
                'type' => 'hospital_publico',
                'latitude' => 10.598500,
                'longitude' => -66.942300,
                'address_description' => 'La Guaira, estado Vargas.',
                'status' => 'operativo',
            ]
        ];

        foreach ($hospitals as $hospitalData) {
            Hospital::updateOrCreate(
                ['name' => $hospitalData['name']],
                $hospitalData
            );
        }
    }
}
