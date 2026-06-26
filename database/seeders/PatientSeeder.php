<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $huc = Hospital::where('name', 'like', '%Universitario%')->first();
        $vargas = Hospital::where('name', 'like', '%Vargas%')->first();
        $perez = Hospital::where('name', 'like', '%Pérez Carreño%')->first();
        $carpa = Hospital::where('name', 'like', '%Carpa%')->first();

        // Si por alguna razón no se encuentran por nombre, usamos IDs por defecto 1 a 4
        $hucId = $huc ? $huc->id : 1;
        $vargasId = $vargas ? $vargas->id : 2;
        $perezId = $perez ? $perez->id : 3;
        $carpaId = $carpa ? $carpa->id : 5;

        // Paciente 1 (Identificado, estable en el HUC)
        Patient::create([
            'hospital_id' => $hucId,
            'dni' => 'V-15894123',
            'first_name' => 'Luis Alberto',
            'last_name' => 'González Rojas',
            'approximate_age' => 42,
            'physical_description' => 'Estatura promedio, cabello negro corto. Vestía camisa azul y jean. Traía reloj metálico plateado en mano izquierda.',
            'medical_status' => 'estable',
            'photo_url' => 'https://r2.juntosvnzl.org/patients/luis_gonzalez.jpg',
        ]);

        // Paciente 2 (Identificado, crítico en Pérez Carreño)
        Patient::create([
            'hospital_id' => $perezId,
            'dni' => 'V-20112443',
            'first_name' => 'Sofia Valentina',
            'last_name' => 'Martínez Crespo',
            'approximate_age' => 26,
            'physical_description' => 'Tez clara, cabello largo castaño. Cicatriz pequeña en la frente. Ingresó inconsciente sin pertenencias.',
            'medical_status' => 'critico',
            'photo_url' => 'https://r2.juntosvnzl.org/patients/sofia_martinez.jpg',
        ]);

        // Paciente 3 (No identificado - Ficha de búsqueda en la Carpa)
        Patient::create([
            'hospital_id' => $carpaId,
            'dni' => null,
            'first_name' => null,
            'last_name' => null,
            'approximate_age' => 65,
            'physical_description' => 'Hombre de la tercera edad, desorientado. Cabello canoso abundante, barba corta. Tatuaje de ancla en el brazo izquierdo. Camiseta blanca rayada.',
            'medical_status' => 'observacion',
            'photo_url' => 'https://r2.juntosvnzl.org/patients/desconocido_1.jpg',
        ]);

        // Paciente 4 (Identificado, dado de alta en Hospital Vargas)
        Patient::create([
            'hospital_id' => $vargasId,
            'dni' => 'V-9874563',
            'first_name' => 'Ramón José',
            'last_name' => 'Mendoza Pérez',
            'approximate_age' => 71,
            'physical_description' => 'Estatura alta, contextura delgada. Usa anteojos recetados.',
            'medical_status' => 'dado_de_alta',
            'photo_url' => null,
        ]);

        // Paciente 5 (Menor de edad, trasladado)
        Patient::create([
            'hospital_id' => $hucId,
            'dni' => null,
            'first_name' => 'Danielito',
            'last_name' => 'Desconocido',
            'approximate_age' => 8,
            'physical_description' => 'Niño de aprox 8 años. Vestía franela roja de rayas y bermuda gris. Rescatado en sector La Vega.',
            'medical_status' => 'trasladado',
            'photo_url' => 'https://r2.juntosvnzl.org/patients/danielito.jpg',
        ]);
    }
}
