<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Service;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Standart', 'price_per_person' => 145000, 'status' => 'Faol', 'description' => 'Asosiy taomlar, salatlar va xizmat ko\'rsatish bilan.'],
            ['name' => 'Premium', 'price_per_person' => 195000, 'status' => 'Faol', 'description' => 'Premium menyu va kengaytirilgan dastur bilan.'],
            ['name' => 'Vip', 'price_per_person' => 245000, 'status' => 'Faol', 'description' => 'VIP menyu, bezak va sahna xizmatlari bilan.'],
        ];

        foreach ($items as $item) {
            MenuItem::updateOrCreate(['name' => $item['name']], $item);
        }

        $services = [
            ['name' => 'DJ va musiqa', 'price' => 3500000, 'status' => 'Faol', 'description' => 'Professional DJ va audio xizmat.'],
            ['name' => 'Foto va video', 'price' => 4200000, 'status' => 'Faol', 'description' => 'Tadbirni to\'liq suratga olish xizmati.'],
            ['name' => 'Dekor', 'price' => 5000000, 'status' => 'Faol', 'description' => 'Sahna va stol bezaklari xizmati.'],
            ['name' => 'Boshlovchi', 'price' => 2800000, 'status' => 'Faol', 'description' => 'Tadbir boshlovchisi xizmati.'],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['name' => $service['name']], $service);
        }
    }
}
