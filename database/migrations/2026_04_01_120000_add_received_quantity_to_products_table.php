<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products') || Schema::hasColumn('products', 'received_quantity')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('received_quantity', 14, 3)->default(0)->after('sku');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'received_quantity')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('received_quantity');
        });
    }
};
