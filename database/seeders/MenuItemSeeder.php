<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Green Garden Salad', 'category' => 'Salads', 'price' => 8.50],
            ['name' => 'Creamy Mushroom Soup', 'category' => 'Starters', 'price' => 7.25],
            ['name' => 'Grilled Salmon Plate', 'category' => 'Main Course', 'price' => 18.90],
            ['name' => 'Steak & Herb Butter', 'category' => 'Main Course', 'price' => 22.75],
            ['name' => 'Chocolate Lava Cake', 'category' => 'Desserts', 'price' => 6.80],
            ['name' => 'Fresh Mint Lemonade', 'category' => 'Beverages', 'price' => 4.20],
        ];

        foreach ($items as $item) {
            $category = Category::where('name', $item['category'])->first();
            if (! $category) {
                continue;
            }

            MenuItem::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id' => $category->id,
                    'description' => $item['name'].' prepared by our kitchen.',
                    'price' => $item['price'],
                    'status' => 'available',
                ]
            );
        }
    }
}
