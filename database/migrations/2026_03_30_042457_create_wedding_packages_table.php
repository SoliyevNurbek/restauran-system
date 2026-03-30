<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wedding_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price_per_person', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status')->default('Faol');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wedding_packages');
    }
};
