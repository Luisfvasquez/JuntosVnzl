<?php

namespace Database\Seeders;

use App\Models\AidOffer;
use App\Models\User;
use Illuminate\Database\Seeder;

class AidOfferSeeder extends Seeder
{
    public function run(): void
    {
        /* $carlos = User::where('name', 'like', '%Carlos%')->first();
        $carlosId = $carlos ? $carlos->id : null;

        // Oferta 1: Carlos Rivas (Voluntario registrado - Transporte)
        AidOffer::create([
            'user_id' => $carlosId,
            'provider_name' => 'Carlos Rivas',
            'dni' => 'V-11223344',
            'phone' => '+584241112233',
            'title' => 'Camión de carga 350 para traslados',
            'description' => 'Ofrezco mi camión 350 con chofer y gasolina para transportar cajas de insumos o agua desde centros de acopio a refugios. Disponible por las tardes.',
            'category' => 'transporte',
            'latitude' => 10.48520000,
            'longitude' => -66.89700000,
            'address_description' => 'Los Chaguaramos, cerca de la UCV. Estacionamiento privado.',
            'status' => 'disponible',
        ]);

        // Oferta 2: Anónimo (Fase I - Comida)
        AidOffer::create([
            'user_id' => null,
            'provider_name' => 'Panadería Gran Meliá',
            'dni' => 'J-31415926',
            'phone' => '+584125556677',
            'title' => '50 raciones de pan fresco y empanadas diarias',
            'description' => 'Donación diaria de pan salado, sobado y empanadas preparadas. Retirar a partir de las 6:00 PM.',
            'category' => 'comida',
            'latitude' => 10.49310000,
            'longitude' => -66.88120000,
            'address_description' => 'Sabana Grande, Avenida Casanova, Local 3B.',
            'status' => 'disponible',
        ]);

        // Oferta 3: Anónimo (Fase I - Salud/Medicamentos - Reservado)
        AidOffer::create([
            'user_id' => null,
            'provider_name' => 'Dra. Elena Silva',
            'dni' => 'V-12987654',
            'phone' => '+584144445555',
            'title' => 'Cajas de Analgésicos y Antiinflamatorios',
            'description' => '3 cajas cerradas de Ibuprofeno y Acetaminofén pediátrico. Lote vence en 2028.',
            'category' => 'salud',
            'latitude' => 10.50500000,
            'longitude' => -66.91420000,
            'address_description' => 'San Bernardino, frente al Hospital de Clínicas Caracas.',
            'status' => 'reservado',
        ]);

        // Oferta 4: Anónimo (Fase I - Ropa - Entregado)
        AidOffer::create([
            'user_id' => null,
            'provider_name' => 'Familia Rodríguez',
            'dni' => 'V-8877665',
            'phone' => '+584261234567',
            'title' => 'Ropa de abrigo y mantas térmicas',
            'description' => 'Mantas y chaquetas en buen estado. Lavadas y embolsadas por tallas.',
            'category' => 'ropa',
            'latitude' => 10.46320000,
            'longitude' => -66.97450000,
            'address_description' => 'Caricuao, sector UD3, bloque 12.',
            'status' => 'entregado',
        ]); */
    }
}
