<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Administrador Principal
        User::create([
            'name' => 'Alejandro Mendoza (SuperAdmin)',
            'dni' => 'V-12345678',
            'phone' => '+584121234567',
            'email' => 'admin@juntosvnzl.org',
            'password' => Hash::make('admin12345'),
            'role' => 'superadmin',
            'is_verified' => true,
        ]);

        // Coordinador de Acopios
        User::create([
            'name' => 'Patricia Delgado (Coordinadora)',
            'dni' => 'V-87654321',
            'phone' => '+584149876543',
            'email' => 'patricia@juntosvnzl.org',
            'password' => Hash::make('coord12345'),
            'role' => 'coordinator',
            'is_verified' => true,
        ]);

        // Voluntario de Campo 1
        User::create([
            'name' => 'Carlos Rivas',
            'dni' => 'V-11223344',
            'phone' => '+584241112233',
            'email' => 'carlos@juntosvnzl.org',
            'password' => Hash::make('voluntario123'),
            'role' => 'volunteer',
            'is_verified' => true,
        ]);

        // Voluntario de Campo 2 (No verificado aún)
        User::create([
            'name' => 'Mariana Torres',
            'dni' => 'V-55667788',
            'phone' => '+584164445566',
            'email' => 'mariana@juntosvnzl.org',
            'password' => Hash::make('voluntario123'),
            'role' => 'volunteer',
            'is_verified' => false,
        ]);
    }
}
