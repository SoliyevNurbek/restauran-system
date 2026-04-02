<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookings') || Schema::hasColumn('bookings', 'currency')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('currency', 3)->default('UZS')->after('price_per_person');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bookings') || ! Schema::hasColumn('bookings', 'currency')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
