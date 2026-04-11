<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('venue_connections')) {
            return;
        }

        if (! Schema::hasColumn('venue_connections', 'is_system_workspace')) {
            Schema::table('venue_connections', function (Blueprint $table): void {
                $table->boolean('is_system_workspace')->default(false)->after('review_reason');
                $table->index('is_system_workspace');
            });
        }

        DB::table('venue_connections')
            ->where('username', 'legacy-tenant')
            ->update(['is_system_workspace' => true]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('venue_connections') || ! Schema::hasColumn('venue_connections', 'is_system_workspace')) {
            return;
        }

        Schema::table('venue_connections', function (Blueprint $table): void {
            $table->dropIndex(['is_system_workspace']);
            $table->dropColumn('is_system_workspace');
        });
    }
};
