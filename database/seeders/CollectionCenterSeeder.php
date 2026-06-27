<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CollectionCenter;

class CollectionCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centers = [
            // Extraídos del primer lote (filtrando los que dicen Hospital)
            ['center_name' => 'U.E Olga Bayone de Rodriguez', 'address' => 'San Diego, Carabobo'],
            ['center_name' => 'Remedios entre todo - Iglesia de la Chiquinquira', 'address' => 'Padre Luis'],
            ['center_name' => 'Lobby Centro Comercial las Acacias', 'address' => 'Valera, Trujillo'],
            ['center_name' => 'Gimnasio Brave Box Barrio Pueblo Nuevo', 'address' => 'Barrio Pueblo Nuevo San Antonio del Táchira, Táchira'],
            ['center_name' => 'Imposs San Antonio del Táchira', 'address' => 'San Antonio del Táchira, Táchira'],
            ['center_name' => 'Osorio Group San Antonio del Táchira', 'address' => 'San Antonio del Táchira, Táchira'],
            ['center_name' => 'Kcho Tours San Antonio del Táchira', 'address' => 'Barrio Ruiz Pineda San Antonio del Táchira, Táchira'],
            ['center_name' => 'Voluntarios Diócesis de San Cristóbal', 'address' => 'Barrio Obrero, San Cristóbal, Táchira'],
            ['center_name' => 'Estadio Didimo Omañana Parroquia el Palotal', 'address' => 'Parroquia el Palotal, Municipio Bolívar, Táchira'],
            ['center_name' => 'Almacenadora Adualca San Antonio del Táchira', 'address' => 'Vía aeropuerto, San Antonio del Táchira, Táchira'],
            ['center_name' => 'OFICINA DE TRANSPORTE CATIA', 'address' => 'EL JUNQUITO, Distrito Capital'],
            ['center_name' => 'SOCIEDAD ANTICANCEROSA', 'address' => 'SAN JUAN DE LOS MORROS, Guárico'],
            ['center_name' => 'Iglesia maranatha caracas', 'address' => 'Sector san Agustín, Distrito Capital'],
            ['center_name' => 'Iglesia La Coromoto', 'address' => 'El Paraíso, Distrito Capital'],
            ['center_name' => 'Colegio La Salle Barquisimeto', 'address' => 'Lara'],
            ['center_name' => 'Centro de Acopio para Mascotas', 'address' => 'Montaña Alta, Miranda'],
            ['center_name' => 'PAYASOS HUMANITARIOS', 'address' => 'Maracaibo, Zulia'],

            // Extraídos del segundo lote (con teléfono)
            ['center_name' => 'Fitness Factory — Galpón', 'address' => 'Calle Urdaneta con Av. Venezuela', 'phone' => '+584128030425'],
            ['center_name' => 'Fitness Factory — West (Oeste)', 'address' => 'C.C. Venrol, Av. Pedro León Torres', 'phone' => '+584245673331'],
            ['center_name' => 'Pollos MB — 380kmvnzla', 'address' => 'Calle 36 esquina carrera 27', 'phone' => '+584245996376'],
            ['center_name' => 'Electro Auto JB — 380kmvnzla', 'address' => 'Carrera 32A entre calles 42 y 43', 'phone' => '+584245996376'],
            ['center_name' => 'Pollera Los Parras — 380kmvnzla', 'address' => 'Av. principal Colinas de San Lorenzo', 'phone' => '+584245996376'],
            ['center_name' => '380kmvnzla Norte — Pollera', 'address' => 'Vía El Cuji, al lado del Colegio Prados del Norte', 'phone' => '+584121945583'],
            ['center_name' => 'Rafa\'s — donaciones en línea', 'address' => 'Donación a distancia', 'phone' => '+584140358377'],
            ['center_name' => 'Hospitour', 'address' => 'Carrera 25 entre calles 14 y 15', 'phone' => '+584245541012'],
            ['center_name' => 'Nevados VE', 'address' => 'Barquisimeto Torre Inter, planta baja', 'phone' => '+584128665924'],
            ['center_name' => 'Future Stars Baseball Academy', 'address' => 'Av. Caroní, edificio Future Stars', 'phone' => '+584145270930'],
            ['center_name' => 'Eleven24', 'address' => 'Barrio Unión, calle 6 entre carreras 19 y 20', 'phone' => '+584125178612'],
            ['center_name' => 'María Corredentora Reina de la Paz', 'address' => 'Yacural, Santa Rosa — casa #20', 'phone' => '+584145590276'],
            ['center_name' => 'Rafa\'s Casual Food / Chicken Box', 'address' => 'Todas sus sedes en Barquisimeto'],
            ['center_name' => 'Fr Fit — Sede Este', 'address' => 'Av. Bracamonte'],
            ['center_name' => 'Fundación Redes Tejiendo', 'address' => 'Calle 41 entre carreras 13 y 14, casa 13-50'],
            ['center_name' => 'C.C Ciudad Paris', 'address' => 'C.C. Ciudad París, Av. Los leones'],
            ['center_name' => 'Universidad Fermín Toro (El Ujano)', 'address' => 'Todas sus sedes habilitadas'],
            ['center_name' => 'Parroquia Nuestra Señora de Guadalupe', 'address' => 'Av. 20 entre calles 40 y 41, Ciudad Crepuscular'],
            ['center_name' => 'MantequillaSport', 'address' => 'Av. Pedro León Torres con calle 46'],
            ['center_name' => 'Universidad Simón Rodríguez', 'address' => 'Av. Argimiro Bracamonte, frente al Parque del Este'],
            ['center_name' => 'Tecnológico Antonio José de Sucre', 'address' => 'Av. 20 entre calles 12 y 13'],
            ['center_name' => 'Bodegón Llanero', 'address' => 'Todas sus sedes en Barquisimeto'],
            ['center_name' => 'Merkafur', 'address' => 'Todas sus sedes en Barquisimeto'],
            ['center_name' => 'Boom Solutions', 'address' => 'Todas sus sedes en Barquisimeto'],
            ['center_name' => 'La Flor de Venezuela', 'address' => 'Av. Venezuela con Av. Bracamonte'],
            ['center_name' => 'Torre Changan', 'address' => 'Av. Bracamonte, Zona Este', 'phone' => '+584144000196'],
            ['center_name' => 'Bhoga Fashion Corner', 'address' => 'Urb. Nueva Segovia, calle 1 entre calles 3 y 4', 'phone' => '+584245649238'],
            ['center_name' => 'Farma Bien', 'address' => 'Av. Vargas entre carreras 22 y 23', 'phone' => '+584247786729'],
            ['center_name' => 'Virtux Box', 'address' => 'Carrera 4 entre calles 18 y 19', 'phone' => '+584127816161'],
            ['center_name' => 'Qué Sanguche', 'address' => 'Av. Venezuela y calle 22, Caminito', 'phone' => '+584147112289'],
            ['center_name' => 'Grupo Ramón Pompilio', 'address' => 'Lara', 'phone' => '+584145553256'],
            ['center_name' => 'Centauros Gym Arca', 'address' => 'C.C. Arca, carrera 32 con calle 20', 'phone' => '+584143527274'],
            ['center_name' => 'West Food and Bar', 'address' => 'Calle 52 entre carreras 14 y 15', 'phone' => '+584245963652'],
            ['center_name' => 'Insuservi', 'address' => 'Calle 34 entre carreras 25 y Av. Venezuela', 'phone' => '+584124607650'],
            ['center_name' => 'Barbería VIP', 'address' => 'C.C. Ciudad París, piso 2'],
            ['center_name' => 'Colegio Río Claro', 'address' => 'Av. Hernán Garmendia'],
            ['center_name' => 'JAC Express Barquisimeto', 'address' => 'Av. 20 con calle 15'],
            ['center_name' => 'Voz Segura', 'address' => 'Edif. Villas del Parque'],
            ['center_name' => 'Sonrisas Sanadoras', 'address' => 'Calle 25 con carreras 17 y 18'],
            ['center_name' => 'Sublime', 'address' => 'C.C. Barquicentro, Av. 20'],
            ['center_name' => 'Colegio Padre Machado', 'address' => 'Av. Lara entre calles 6 y 7, Urb. Nueva Segovia'],
            ['center_name' => 'El Maizalito', 'address' => 'Av. Carlos Giffoni, Zona Industrial III'],
            ['center_name' => 'Un Nuevo Tiempo Lara', 'address' => 'Carrera 15 esquina calle 40'],
            ['center_name' => 'Colegio San Pedro', 'address' => 'Carrera 18, Barquisimeto'],
            ['center_name' => 'Fitness Factory — Cabudare', 'address' => 'Av. El Placer, C.C. Hipermercado', 'phone' => '+584149183003'],
            ['center_name' => 'Casapro', 'address' => 'Cabudare', 'phone' => '+584149543382'],
            ['center_name' => 'Campus Mora I — Yacambú', 'address' => 'Yacambú, Cabudare', 'phone' => '+584120283656'],
            ['center_name' => 'Universidad Martin Luther King', 'address' => 'Sede en Cabudare'],
            ['center_name' => 'TodoPollo\'s (AIEM Rescatistas)', 'address' => 'Av. Principal de La Mata entre calles 4 y 5'],
            ['center_name' => 'Urb. Las Mercedes — Panadería San Benito', 'address' => 'Av. La Montañita'],
            ['center_name' => 'Gráficas Alvarado C.A.', 'address' => 'Av. Santa Bárbara, Cabudare Centro'],
        ];

        // Simulamos lat/lng alrededor de Barquisimeto (la mayoría de la lista 2) y variadas para llenar la data
        $baseLat = 10.0678;
        $baseLng = -69.3474;

        foreach ($centers as $index => $centerData) {
            
            $phone = $centerData['phone'] ?? '+580000000000';
            
            CollectionCenter::updateOrCreate(
                ['center_name' => $centerData['center_name']],
                [
                    'manager_name' => 'Voluntario Designado', // Default falso
                    'dni' => 'V-0000000' . $index, // DNI dummy único
                    'phone' => $phone,
                    'address_description' => $centerData['address'],
                    'latitude' => $baseLat + ($index * 0.001), // Variar un poco
                    'longitude' => $baseLng + ($index * 0.001),
                    'status' => 'recibiendo_insumos'
                ]
            );
        }
    }
}
