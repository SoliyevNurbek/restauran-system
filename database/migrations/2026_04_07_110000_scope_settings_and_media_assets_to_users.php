<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->unique('user_id');
        });

        Schema::table('media_assets', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->foreignId('owner_user_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->unique(['owner_user_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::table('media_assets', function (Blueprint $table) {
            $table->dropUnique(['owner_user_id', 'key']);
            $table->dropConstrainedForeignId('owner_user_id');
            $table->unique('key');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
