<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'restaurant_name' => 'Javohir Restoran CRM',
                'theme_preference' => 'light',
            ]
        );
    }
}
