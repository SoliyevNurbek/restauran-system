<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label')->nullable();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        if (Schema::hasTable('settings')) {
            $setting = DB::table('settings')->first();

            if ($setting?->logo_path) {
                DB::table('media_assets')->insert([
                    'key' => 'brand_logo',
                    'label' => 'Brend logotipi',
                    'disk' => 'public',
                    'path' => $setting->logo_path,
                    'alt_text' => 'Brend logotipi',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($setting?->favicon_path) {
                DB::table('media_assets')->insert([
                    'key' => 'brand_favicon',
                    'label' => 'Favicon',
                    'disk' => 'public',
                    'path' => $setting->favicon_path,
                    'alt_text' => 'Favicon',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
