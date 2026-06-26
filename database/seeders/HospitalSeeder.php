<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        Hospital::create([
            'name' => 'Hospital Universitario de Caracas (HUC)',
            'type' => 'hospital_publico',
            'latitude' => 10.48910000,
            'longitude' => -66.89030000,
            'address_description' => 'Ciudad Universitaria, Los Chaguaramos. Entrada por el área de emergencias.',
            'status' => 'operativo',
            'contact_phone' => '+582126067111',
            'image_url' => 'https://r2.juntosvnzl.org/hospitals/huc.jpg',
        ]);

        Hospital::create([
            'name' => 'Hospital Vargas de Caracas',
            'type' => 'hospital_publico',
            'latitude' => 10.51520000,
            'longitude' => -66.91180000,
            'address_description' => 'Monte Carmelo a San Pirito, San José, Caracas.',
            'status' => 'sin_insumos',
            'contact_phone' => '+582125629333',
            'image_url' => 'https://r2.juntosvnzl.org/hospitals/hospital_vargas.jpg',
        ]);

        Hospital::create([
            'name' => 'Hospital Dr. Miguel Pérez Carreño',
            'type' => 'hospital_publico',
            'latitude' => 10.47890000,
            'longitude' => -66.95310000,
            'address_description' => 'Calle Principal de La Yaguara, frente a la estación de Metro.',
            'status' => 'colapsado',
            'contact_phone' => '+582124725055',
            'image_url' => 'https://r2.juntosvnzl.org/hospitals/perez_carreno.jpg',
        ]);

        Hospital::create([
            'name' => 'Clínica Metropolitana (Privada)',
            'type' => 'clinica_privada',
            'latitude' => 10.48120000,
            'longitude' => -66.81880000,
            'address_description' => 'Calle A-1, Urbanización Caurimare, Caracas.',
            'status' => 'operativo',
            'contact_phone' => '+582129080111',
            'image_url' => 'https://r2.juntosvnzl.org/hospitals/clinica_metro.jpg',
        ]);

        Hospital::create([
            'name' => 'Carpa de Campaña - Plaza Venezuela',
            'type' => 'carpa_campana',
            'latitude' => 10.49650000,
            'longitude' => -66.88580000,
            'address_description' => 'Instalada en la plaza sur de Plaza Venezuela, al lado de la estación del metro.',
            'status' => 'operativo',
            'contact_phone' => null,
            'image_url' => null,
        ]);
    }
}
