<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wedding_package_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedding_package_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $packages = DB::table('wedding_packages')
            ->whereNotNull('image')
            ->select('id', 'image')
            ->get();

        foreach ($packages as $package) {
            DB::table('wedding_package_images')->insert([
                'wedding_package_id' => $package->id,
                'image_path' => $package->image,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wedding_package_images');
    }
};
