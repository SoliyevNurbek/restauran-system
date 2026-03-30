<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'John Carter', 'phone' => '+1 555 1010'],
            ['name' => 'Emily Stone', 'phone' => '+1 555 2222'],
            ['name' => 'Michael Lee', 'phone' => '+1 555 3321'],
            ['name' => 'Sophia Adams', 'phone' => '+1 555 7788'],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(['name' => $customer['name']], $customer);
        }
    }
}
