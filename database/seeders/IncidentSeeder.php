<?php

namespace Database\Seeders;

use App\Models\Incident;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        Incident::create([
            'reporter_name' => 'Luis Rengifo',
            'description' => 'Deslizamiento de tierra masivo de la colina. Obstruye los dos canales de la autopista. Paso completamente bloqueado.',
            'latitude' => 10.45680000,
            'longitude' => -66.92310000,
            'address_description' => 'Autopista Regional del Centro (ARC), Kilómetro 8, sentido Caracas.',
            'severity' => 'critico',
            'aid_status' => 'ayuda_oficial_en_sitio',
            'aid_type_received' => 'Maquinaria pesada del Ministerio del Transporte removiendo escombros.',
            'verified_by_community_count' => 18,
        ]);

        Incident::create([
            'reporter_name' => 'Anónimo',
            'description' => 'Inundación severa por desbordamiento del río Guaire. El agua ingresó a las casas de planta baja. Pérdidas materiales de enseres.',
            'latitude' => 10.48550000,
            'longitude' => -66.82420000,
            'address_description' => 'Sector La Línea, Petare, Municipio Sucre.',
            'severity' => 'catastrofico',
            'aid_status' => 'ayuda_civil_en_sitio',
            'aid_type_received' => 'Vecinos organizados y bomberos voluntarios con lanchas rescatando enseres e infantes.',
            'verified_by_community_count' => 42,
        ]);

        Incident::create([
            'reporter_name' => 'Yolanda Silva',
            'description' => 'Caída de árbol de gran tamaño sobre tendido eléctrico. Dejó a 3 cuadras sin luz y obstruye un canal secundario.',
            'latitude' => 10.49980000,
            'longitude' => -66.87410000,
            'address_description' => 'Avenida Principal de La Floresta, cruce con Calle Delicias.',
            'severity' => 'leve',
            'aid_status' => 'sin_ayuda',
            'aid_type_received' => null,
            'verified_by_community_count' => 3,
        ]);

        Incident::create([
            'reporter_name' => 'Marcos Peña',
            'description' => 'Colapso parcial del puente peatonal debido a socavación de la base por lluvias torrenciales.',
            'latitude' => 10.47050000,
            'longitude' => -66.90120000,
            'address_description' => 'Paso peatonal entre Valle Abajo y Los Chaguaramos, sobre el río.',
            'severity' => 'moderado',
            'aid_status' => 'ayuda_civil_en_sitio',
            'aid_type_received' => 'Cordonamiento con cinta de peligro colocado por grupo scout local.',
            'verified_by_community_count' => 9,
        ]);
    }
}
