<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            HospitalSeeder::class,
            PatientSeeder::class,
            CountryAidSeeder::class,
            IncidentSeeder::class,
            CollectionCenterSeeder::class,
            CenterSupplySeeder::class,
            AidOfferSeeder::class,
        ]);
    }
}
