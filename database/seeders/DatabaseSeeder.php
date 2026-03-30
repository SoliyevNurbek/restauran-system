<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            AdminUserSeeder::class,
            CategorySeeder::class,
            MenuItemSeeder::class,
            DiningTableSeeder::class,
            CustomerSeeder::class,
            StaffSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
