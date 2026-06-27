<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CollectionCenter;

class ProdCollectionCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centers = [
            ['center_name' => 'Iglesia La Paz', 'address' => 'Montalbán I, Municipio Libertador', 'latitude' => 10.485993, 'longitude' => -66.9755784],
            ['center_name' => 'Iglesia San Bernardino de Siena', 'address' => 'Parroquia San Bernardino', 'latitude' => 10.5139151, 'longitude' => -66.9017189],
            ['center_name' => 'Terrazas del Club Hípico (Rotaract Caracas)', 'address' => 'Club Hípico, Caracas', 'latitude' => 10.44752, 'longitude' => -66.8707033],
            ['center_name' => 'Cáritas Venezuela — Sede Montalbán', 'address' => 'Av. Teherán, a 200 m de la UCAB, frente a urb. Juan Pablo II, Montalbán', 'latitude' => 10.4673695, 'longitude' => -66.9671262],
            ['center_name' => 'G3 Logística — Los Cortijos de Lourdes', 'address' => 'Av. Principal de Los Cortijos de Lourdes, Edif. Maploca', 'latitude' => 10.472965, 'longitude' => -66.9929093],
            ['center_name' => 'Primero Justicia (FCU-UCV)', 'address' => 'Los Palos Grandes, 3.ª transversal, Edif. Primero Justicia', 'latitude' => 10.2417099, 'longitude' => -67.8287549],
            ['center_name' => 'AJE Venezuela — Polideportivo Los Naranjos', 'address' => 'Belisario Academy, Av. Este 3, Los Naranjos', 'latitude' => 10.4420359, 'longitude' => -66.8356199],
            ['center_name' => 'Cuarta avenida de Altamira', 'address' => 'Cuarta avenida de Altamira, entre 9.ª y 10.ª transversal; quinta El Bejucal', 'latitude' => 10.5106087, 'longitude' => -66.8528768],
            ['center_name' => 'Colegio Cristo Rey Altamira', 'address' => '7.ª Avenida de Altamira, entre 6.ª y 7.ª Transversal', 'latitude' => 10.5057299, 'longitude' => -66.8490399],
            ['center_name' => 'Colegio Mater Salvatoris', 'address' => 'Las Mercedes, Caracas', 'latitude' => 10.485242, 'longitude' => -66.8660811],
            ['center_name' => 'Club Juvenil Guayacán', 'address' => 'Calle La Esmeralda, La Tahona', 'latitude' => 10.4337686, 'longitude' => -66.8546558],
            ['center_name' => 'Academia Merici', 'address' => 'Cerro Verde, El Hatillo', 'latitude' => 10.4546776, 'longitude' => -66.8387982],
            ['center_name' => 'EPA La Trinidad', 'address' => 'Av. Intercomunal de La Trinidad, Baruta', 'latitude' => 10.4326584, 'longitude' => -66.8706555],
            ['center_name' => 'Ferretotal La Trinidad', 'address' => 'La Trinidad, Baruta', 'latitude' => 10.4311102, 'longitude' => -66.85038],
            ['center_name' => 'Instituto de Diseño de Caracas', 'address' => 'El Rosal, Caracas', 'latitude' => 10.4971079, 'longitude' => -66.8532373],
            ['center_name' => 'Fitness Factory', 'address' => 'Los Palos Grandes, Caracas', 'latitude' => 10.5025223, 'longitude' => -66.8465744],
            ['center_name' => 'The Place', 'address' => 'Los Palos Grandes, Caracas', 'latitude' => 10.5000478, 'longitude' => -66.844026],
            ['center_name' => 'Tributo', 'address' => 'Los Palos Grandes, Caracas', 'latitude' => 10.5041297, 'longitude' => -66.8459996],
            ['center_name' => '4.ª Transversal de Los Palos Grandes', 'address' => '4.ª Transversal, Los Palos Grandes, Caracas', 'latitude' => 10.4970415, 'longitude' => -66.8280079],
            ['center_name' => 'La Esfera de Soto', 'address' => 'Autopista Francisco Fajardo, Chacao', 'latitude' => 10.4874061, 'longitude' => -66.8388157],
            ['center_name' => 'Copa Hatillana', 'address' => 'Pueblo de El Hatillo', 'latitude' => 10.4247555, 'longitude' => -66.826435],
            ['center_name' => 'Colegio Santo Tomás de Aquino', 'address' => 'Caracas', 'latitude' => 10.4966372, 'longitude' => -66.8606561],
            ['center_name' => 'Mingus', 'address' => 'Chacao', 'latitude' => 10.495648, 'longitude' => -66.8537642],
            ['center_name' => 'Centro de Tata', 'address' => 'Bello Campo, Chacao', 'latitude' => 10.4946514, 'longitude' => -66.8505523],
            ['center_name' => 'Plaza Alfredo Sadel (Alcaldía de Baruta)', 'address' => 'Las Mercedes, Baruta', 'latitude' => 10.4587791, 'longitude' => -66.8662973],
            ['center_name' => 'Protección Civil El Cafetal (Alcaldía de Baruta)', 'address' => 'El Cafetal, Baruta', 'latitude' => 10.4570532, 'longitude' => -66.8256299],
            ['center_name' => 'Policía de Baruta', 'address' => 'Sede de PoliBaruta', 'latitude' => 10.4575632, 'longitude' => -66.8626323],
            ['center_name' => 'Avenida 19 de abril, Centro Comercial La Capilla', 'address' => 'Maracay', 'latitude' => 10.243345, 'longitude' => -67.5656733],
            ['center_name' => 'Paseo de la Libertad, Av. Las Delicias', 'address' => 'Maracay', 'latitude' => 10.2589583, 'longitude' => -67.5840157],
            ['center_name' => 'Avenida Monseñor Adams, El Viñedo', 'address' => 'Valencia', 'latitude' => 10.2138215, 'longitude' => -68.0135071],
            ['center_name' => 'G3 Logística — Valencia', 'address' => 'Calle La Pedrera, Fundo Los Marines, Zona Industrial San Diego', 'latitude' => 10.1918441, 'longitude' => -67.9747402],
            ['center_name' => 'Sun Risas — Parque Dunas', 'address' => 'Parque Dunas, Valencia', 'latitude' => 10.2324049, 'longitude' => -68.0050311],
            ['center_name' => 'Cuadro', 'address' => 'Centro Comercial La Grieta, Valencia', 'latitude' => 10.212272, 'longitude' => -68.0165165],
            ['center_name' => 'Tatas Food', 'address' => 'Carrera 15 entre calles 13A y 13B, Barquisimeto', 'latitude' => 10.066597, 'longitude' => -69.3661424],
            ['center_name' => 'G3 Logística — Barquisimeto', 'address' => 'Zona Industrial II, Av. Principal con Calle 6, locales 110-111-112, Iribarren', 'latitude' => 10.0811597, 'longitude' => -69.3733267],
            ['center_name' => 'Esquina de Banesco, Av. República', 'address' => 'Ciudad Bolívar', 'latitude' => 8.1241567, 'longitude' => -63.5475494],
            ['center_name' => 'Calle 6, antigua Bermúdez, casa N.º 11', 'address' => 'Maturín', 'latitude' => 9.7462006, 'longitude' => -63.1691399],
            ['center_name' => 'Universidad de Los Andes — Núcleo Táchira (ULA)', 'address' => 'San Cristóbal', 'latitude' => 7.7915637, 'longitude' => -72.2133327],
            ['center_name' => 'Un Nuevo Tiempo (UNT) — Sede regional Maracaibo', 'address' => 'Maracaibo', 'latitude' => 10.681566, 'longitude' => -71.6039025],
            ['center_name' => 'Vente Zulia', 'address' => 'Calle 70 con Av. 15A y 15B, N.º 15A-39, Maracaibo', 'latitude' => 10.6700187, 'longitude' => -71.622055],
            ['center_name' => 'Fermaca Express', 'address' => 'Avenida Delicias, Maracaibo', 'latitude' => 10.684989, 'longitude' => -71.6240368],
            ['center_name' => 'Operación Todos con VZLA', 'address' => 'Calle 104 #54-31, Barrio Pasadena, Suba, Bogotá', 'latitude' => 4.6925913, 'longitude' => -74.063494],
            ['center_name' => 'Juntos Se Puede', 'address' => 'Calle 104 #53-31, Bogotá', 'latitude' => 4.6926005, 'longitude' => -74.0634947],
            ['center_name' => 'Global Empowerment Mission · We Love Foundation', 'address' => '1850 NW 84th Ave, Miami', 'latitude' => 25.7910992, 'longitude' => -80.3330184],
            ['center_name' => 'Parroquia Sagrada Familia', 'address' => 'La Tahona, Baruta', 'latitude' => 10.4384215, 'longitude' => -66.8566303],
            ['center_name' => 'Concha Acústica de Bello Monte', 'address' => 'Bello Monte, Caracas', 'latitude' => 10.4802415, 'longitude' => -66.8808288],
            ['center_name' => 'Acción Solidaria', 'address' => 'Bello Monte, Caracas', 'latitude' => 10.4900241, 'longitude' => -66.8756492],
            ['center_name' => 'Pepitería 13', 'address' => 'Las Mercedes, Caracas', 'latitude' => 10.4832106, 'longitude' => -66.8597342],
            ['center_name' => 'Quinta Más Allá', 'address' => 'La Castellana, Chacao', 'latitude' => 10.5036868, 'longitude' => -66.8545036],
            ['center_name' => 'Bajo el Árbol (Quinta Arboleda)', 'address' => 'La Castellana, Chacao', 'latitude' => 10.4788155, 'longitude' => -66.9478708],
            ['center_name' => 'REX', 'address' => 'El Rosal, Chacao', 'latitude' => 10.4874312, 'longitude' => -66.8644995],
            ['center_name' => 'Capellanía UNIMET', 'address' => 'Terrazas del Ávila, Municipio Sucre', 'latitude' => 10.4998486, 'longitude' => -66.7841875],
            ['center_name' => 'Base de operaciones Tu Gruero', 'address' => 'Av. El Carmen, Los Dos Caminos, Sucre', 'latitude' => 10.497567, 'longitude' => -66.8566818],
            ['center_name' => 'Universidad Monteávila', 'address' => 'Boleíta Norte, Municipio Sucre', 'latitude' => 10.5026525, 'longitude' => -66.8177383],
            ['center_name' => 'Cáritas Petare', 'address' => 'Lomas del Sol, Petare, Sucre', 'latitude' => 10.4776218, 'longitude' => -66.8096464],
            ['center_name' => 'Hotel Ávila', 'address' => 'San Bernardino, Caracas', 'latitude' => 10.5166765, 'longitude' => -66.8988172],
            ['center_name' => 'FCU-UCV (Edificio del Rectorado)', 'address' => 'Ciudad Universitaria, UCV, Libertador', 'latitude' => 10.4896457, 'longitude' => -66.8916007],
        ];

        foreach ($centers as $index => $centerData) {
            // Usamos updateOrCreate por nombre para no duplicar si se corre varias veces
            CollectionCenter::updateOrCreate(
                ['center_name' => $centerData['center_name']],
                [
                    'manager_name' => 'Voluntario Designado (Prod)', 
                    'dni' => 'V-999999' . str_pad($index, 2, '0', STR_PAD_LEFT), 
                    'phone' => '+580000000000',
                    'address_description' => $centerData['address'],
                    'latitude' => $centerData['latitude'],
                    'longitude' => $centerData['longitude'],
                    'status' => 'recibiendo_insumos'
                ]
            );
        }
    }
}
