<?php

namespace Database\Seeders;

use App\Models\DiningTable;
use Illuminate\Database\Seeder;

class DiningTableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            DiningTable::updateOrCreate(
                ['table_number' => (string) $i],
                ['status' => $i <= 3 ? 'occupied' : 'free']
            );
        }
    }
}
