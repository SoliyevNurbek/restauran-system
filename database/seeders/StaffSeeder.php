<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staff = [
            ['name' => 'Alice Manager', 'email' => 'alice@restaurant.test', 'phone' => '+1 555 4001', 'role' => 'admin'],
            ['name' => 'Bob Waiter', 'email' => 'bob@restaurant.test', 'phone' => '+1 555 4002', 'role' => 'waiter'],
            ['name' => 'Chris Cashier', 'email' => 'chris@restaurant.test', 'phone' => '+1 555 4003', 'role' => 'cashier'],
        ];

        foreach ($staff as $member) {
            Staff::updateOrCreate(['email' => $member['email']], $member);
        }
    }
}
