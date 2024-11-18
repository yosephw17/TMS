<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialsSeeder extends Seeder
{
    public function run()
    {
        DB::table('materials')->insert([
            [
                'id' => 5,
                'name' => 'L-Profile',
                'code' => null,
                'symbol' => 'materials/uD451ET1eJDHy4evqcYIoIpPm0vC1G7PqnZH0Ac5.png',
                'color' => 'black',
                'unit_price' => 3460.20,
                'unit_of_measurement' => 'bar',
                'type' => 'aluminium_profile', // Example type
                'created_at' => '2024-10-23 06:29:17',
                'updated_at' => '2024-10-23 06:29:17',
            ],
            [
                'id' => 6,
                'name' => 'T-Profile',
                'code' => null,
                'symbol' => 'materials/2m33s8omJzXnqD5uGlaEq8RicGthBhkxxghi7Lwv.png',
                'color' => 'black',
                'unit_price' => 4007.70,
                'unit_of_measurement' => 'bar',
                'type' => 'aluminium_profile', // Example type
                'created_at' => '2024-10-23 06:30:01',
                'updated_at' => '2024-10-23 06:30:01',
            ],
            [
                'id' => 7,
                'name' => 'Z-Profile',
                'code' => null,
                'symbol' => 'materials/t1vVTAvbAMzT4M8Fo0AlUv7NixwoejgVsNiYYyZ1.png',
                'color' => 'black',
                'unit_price' => 5000.00,
                'unit_of_measurement' => 'bar',
                'type' => 'aluminium_profile', // Example type
                'created_at' => '2024-10-23 06:31:00',
                'updated_at' => '2024-10-23 06:31:00',
            ],
            [
                'id' => 8,
                'name' => 'I-Profile',
                'code' => null,
                'symbol' => 'materials/MaV5iOxOjZfDauHj1wrzo6j0PaI5KKTW3wKTpR2F.png',
                'color' => 'black',
                'unit_price' => 2000.00,
                'unit_of_measurement' => 'bar',
                'type' => 'aluminium_profile', // Example type
                'created_at' => '2024-10-23 06:33:30',
                'updated_at' => '2024-10-23 06:33:30',
            ],
            [
                'id' => 9,
                'name' => 'Angle-Profile',
                'code' => null,
                'symbol' => 'materials/mcbI56kK8DoF2dwG6kViLFJxAzG2E56iMlh2JHe3.png',
                'color' => 'black',
                'unit_price' => 1900.50,
                'unit_of_measurement' => 'bar',
                'type' => 'aluminium_profile', // Example type
                'created_at' => '2024-10-23 06:34:15',
                'updated_at' => '2024-10-23 06:34:15',
            ],
            [
                'id' => 10,
                'name' => 'Round Bar',
                'code' => null,
                'symbol' => 'materials/Ab9UlVf38rW5ouuZX1IhhqCj32eHk3a2IEa8uulq.png',
                'color' => 'black',
                'unit_price' => 4500.25,
                'unit_of_measurement' => 'bar',
                'type' => 'aluminium_profile', // Example type
                'created_at' => '2024-10-23 06:35:00',
                'updated_at' => '2024-10-23 06:35:00',
            ],
            [
                'id' => 11,
                'name' => 'Square Bar',
                'code' => null,
                'symbol' => 'materials/1shwn59gbtptw51zG3XcN1wz8hvnI7K2Xy6XTYmc.png',
                'color' => 'black',
                'unit_price' => 3750.75,
                'unit_of_measurement' => 'bar',
                'type' => 'aluminium_profile', // Example type
                'created_at' => '2024-10-23 06:36:00',
                'updated_at' => '2024-10-23 06:36:00',
            ],
            [
                'id' => 12,
                'name' => 'Flat Bar',
                'code' => null,
                'symbol' => 'materials/VN2Hh8dF5ovBFcY3RVfpAYdd8joHZ2RfY21g5dpw.png',
                'color' => 'black',
                'unit_price' => 3950.90,
                'unit_of_measurement' => 'bar',
                'type' => 'aluminium_profile', // Example type
                'created_at' => '2024-10-23 06:37:00',
                'updated_at' => '2024-10-23 06:37:00',
            ],
            [
                'id' => 13,
                'name' => 'Hexagon Bar',
                'code' => null,
                'symbol' => 'materials/sYNh6dQ8ljGbdJ6td6nmDqV9a5t2Nz98FhoUMkmd.png',
                'color' => 'black',
                'unit_price' => 3200.00,
                'unit_of_measurement' => 'bar',
                'type' => 'aluminium_profile', // Example type
                'created_at' => '2024-10-23 06:38:00',
                'updated_at' => '2024-10-23 06:38:00',
            ],
            [
                'id' => 14,
                'name' => 'Copper Wire',
                'code' => null,
                'symbol' => 'materials/pP0yZhhJhGbNfrWGzfiF3N2j5oF4UNnpxBTe70zk.png',
                'color' => 'red',
                'unit_price' => 1250.30,
                'unit_of_measurement' => 'kg',
                'type' => 'aluminium_profile', // Example type
                'created_at' => '2024-10-23 06:39:00',
                'updated_at' => '2024-10-23 06:39:00',
            ],
            [
                'id' => 15,
                'name' => 'Aluminum Sheet',
                'code' => null,
                'symbol' => 'materials/4j5Mtd6Z1VeA0Pj69DrlmWceZcmvL9T7REYoOtuh.png',
                'color' => 'silver',
                'unit_price' => 2250.50,
                'unit_of_measurement' => 'm',
                'type' => 'aluminium_profile', // Example type
                'created_at' => '2024-10-23 06:40:00',
                'updated_at' => '2024-10-23 06:40:00',
            ]
        ]);
    }
}
