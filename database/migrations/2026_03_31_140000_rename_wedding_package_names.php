<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('wedding_packages')->where('name', 'Silver paket')->update(['name' => 'Standart']);
        DB::table('wedding_packages')->where('name', 'Gold paket')->update(['name' => 'Premium']);
        DB::table('wedding_packages')->where('name', 'Platinum paket')->update(['name' => 'Vip']);
        DB::table('wedding_packages')->where('name', 'Silver')->update(['name' => 'Standart']);
        DB::table('wedding_packages')->where('name', 'Gold')->update(['name' => 'Premium']);
        DB::table('wedding_packages')->where('name', 'Platinum')->update(['name' => 'Vip']);
    }

    public function down(): void
    {
        DB::table('wedding_packages')->where('name', 'Standart')->update(['name' => 'Silver paket']);
        DB::table('wedding_packages')->where('name', 'Premium')->update(['name' => 'Gold paket']);
        DB::table('wedding_packages')->where('name', 'Vip')->update(['name' => 'Platinum paket']);
    }
};
