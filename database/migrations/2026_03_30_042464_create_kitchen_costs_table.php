<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kitchen_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('cost_categories')->nullOnDelete();
            $table->string('product_name');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->decimal('gas_cost', 12, 2)->default(0);
            $table->decimal('electric_cost', 12, 2)->default(0);
            $table->decimal('salary_cost', 12, 2)->default(0);
            $table->decimal('tax_share', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_costs');
    }
};
