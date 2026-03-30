<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Toy', 'description' => 'Klassik to\'y marosimlari uchun.'],
            ['name' => 'Nikoh oqshomi', 'description' => 'Nikoh va oila davrasi kechalari uchun.'],
            ['name' => 'Fotiha', 'description' => 'Fotiha va kichik oilaviy tadbirlar uchun.'],
            ['name' => 'Tugilgan kun', 'description' => 'Bolalar va kattalar tavallud bayramlari uchun.'],
            ['name' => 'Yubiley', 'description' => 'Yubiley va maxsus sana tadbirlari uchun.'],
            ['name' => 'Korporativ tadbir', 'description' => 'Kompaniya tadbirlari va rasmiy marosimlar uchun.'],
            ['name' => 'Boshqa marosim', 'description' => 'Boshqa turdagi buyurtma tadbirlari uchun.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
