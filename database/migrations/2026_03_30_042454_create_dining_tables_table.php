<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('dining_tables')) {
            return;
        }

        Schema::create('dining_tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number')->unique();
            $table->enum('status', ['free', 'occupied'])->default('free');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_tables');
    }
};
