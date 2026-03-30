<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['full_name' => 'Azizbek Karimov', 'phone' => '+998901112233', 'extra_phone' => '+998934445566', 'address' => 'Toshkent shahar, Chilonzor', 'passport_info' => 'AA1234567', 'notes' => 'Asosiy to\'lovchi.'],
            ['full_name' => 'Dilnoza Rahimova', 'phone' => '+998901234567', 'extra_phone' => '+998935551122', 'address' => 'Samarqand shahar', 'passport_info' => 'AB7654321', 'notes' => 'Fotiha marosimi uchun mijoz.'],
            ['full_name' => 'Bekzod Islomov', 'phone' => '+998977778899', 'extra_phone' => null, 'address' => 'Buxoro shahar', 'passport_info' => 'AC9081726', 'notes' => 'Korporativ tadbir buyurtmachisi.'],
            ['full_name' => 'Madina Tursunova', 'phone' => '+998998887766', 'extra_phone' => '+998901119988', 'address' => 'Andijon viloyati', 'passport_info' => 'AD4567891', 'notes' => 'Yubiley tadbiri uchun murojaat qilgan.'],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(['full_name' => $customer['full_name']], $customer);
        }
    }
}
