<?php

namespace Database\Seeders;

use App\Models\DiningTable;
use Illuminate\Database\Seeder;

class DiningTableSeeder extends Seeder
{
    public function run(): void
    {
        $halls = [
            ['name' => 'Marvarid zali', 'capacity' => 180, 'price' => 25000000, 'status' => 'Faol', 'description' => 'Katta to\'y va marosimlar uchun premium zal.'],
            ['name' => 'Samarqand zali', 'capacity' => 250, 'price' => 32000000, 'status' => 'Faol', 'description' => 'Keng sahna va katta mehmon sig\'imiga ega zal.'],
            ['name' => 'Family Hall', 'capacity' => 90, 'price' => 14000000, 'status' => 'Faol', 'description' => 'Oilaviy marosimlar va fotiha uchun qulay zal.'],
            ['name' => 'Navro\'z zali', 'capacity' => 140, 'price' => 21000000, 'status' => "Ta'mirda", 'description' => 'Yangilanayotgan zamonaviy marosim zali.'],
        ];

        foreach ($halls as $hall) {
            DiningTable::updateOrCreate(['name' => $hall['name']], $hall);
        }
    }
}
