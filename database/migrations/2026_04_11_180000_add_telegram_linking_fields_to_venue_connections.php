<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('venue_connections')) {
            return;
        }

        Schema::table('venue_connections', function (Blueprint $table) {
            if (! Schema::hasColumn('venue_connections', 'telegram_verified_at')) {
                $table->timestamp('telegram_verified_at')->nullable()->after('telegram_linked_at');
            }

            if (! Schema::hasColumn('venue_connections', 'telegram_link_token')) {
                $table->string('telegram_link_token', 80)->nullable()->after('telegram_verified_at');
            }

            if (! Schema::hasColumn('venue_connections', 'telegram_notifications_enabled')) {
                $table->boolean('telegram_notifications_enabled')->default(true)->after('telegram_link_token');
            }
        });

        DB::table('venue_connections')
            ->whereNull('telegram_link_token')
            ->orderBy('id')
            ->get(['id'])
            ->each(function (object $row): void {
                DB::table('venue_connections')
                    ->where('id', $row->id)
                    ->update([
                        'telegram_link_token' => Str::lower(Str::random(32)),
                    ]);
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('venue_connections')) {
            return;
        }

        Schema::table('venue_connections', function (Blueprint $table) {
            foreach (['telegram_verified_at', 'telegram_link_token', 'telegram_notifications_enabled'] as $column) {
                if (Schema::hasColumn('venue_connections', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
