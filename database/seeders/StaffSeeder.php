<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staff = [
            ['full_name' => 'Sardor Xasanov', 'phone' => '+998901110000', 'role' => 'Administrator', 'salary' => 7000000, 'status' => 'Faol', 'notes' => 'Asosiy administrator.'],
            ['full_name' => 'Munisa Qodirova', 'phone' => '+998901110001', 'role' => 'Menejer', 'salary' => 5500000, 'status' => 'Faol', 'notes' => 'Bron va mijozlar bilan ishlaydi.'],
            ['full_name' => 'Abror Yoqubov', 'phone' => '+998901110002', 'role' => 'Kassir', 'salary' => 4800000, 'status' => 'Faol', 'notes' => 'To\'lovlar hisobi.'],
            ['full_name' => 'Shahnoza Ergasheva', 'phone' => '+998901110003', 'role' => 'Dekorator', 'salary' => 5200000, 'status' => 'Faol', 'notes' => 'Marosim bezaklari.'],
        ];

        foreach ($staff as $member) {
            Staff::updateOrCreate(['full_name' => $member['full_name']], $member);
        }
    }
}
