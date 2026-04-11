<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tenantTables = [
        'clients',
        'halls',
        'event_types',
        'wedding_packages',
        'wedding_package_images',
        'services',
        'bookings',
        'booking_services',
        'payments',
        'employees',
        'cost_categories',
        'kitchen_costs',
        'event_costs',
        'fixed_costs',
        'suppliers',
        'products',
        'purchases',
        'purchase_items',
        'supplier_payments',
        'expense_categories',
        'expenses',
        'booking_usage_items',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('venue_connections')) {
            return;
        }

        foreach ($this->tenantTables as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'venue_connection_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table): void {
                $table->foreignId('venue_connection_id')->nullable()->after('id')->constrained()->nullOnDelete();
                $table->index('venue_connection_id');
            });
        }

        $legacyVenueId = $this->resolveLegacyVenueId();

        if (! $legacyVenueId) {
            return;
        }

        foreach ($this->tenantTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'venue_connection_id')) {
                DB::table($table)->whereNull('venue_connection_id')->update([
                    'venue_connection_id' => $legacyVenueId,
                ]);
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'venue_connection_id')) {
            DB::table('users')
                ->where('role', '!=', 'superadmin')
                ->whereNull('venue_connection_id')
                ->update(['venue_connection_id' => $legacyVenueId]);
        }

        if (Schema::hasTable('venue_connections')) {
            DB::table('venue_connections')
                ->where('id', $legacyVenueId)
                ->whereNull('admin_user_id')
                ->update([
                    'admin_user_id' => DB::table('users')
                        ->where('role', '!=', 'superadmin')
                        ->where('venue_connection_id', $legacyVenueId)
                        ->value('id'),
                ]);
        }

        $this->replaceUniqueIndex('products', 'products_sku_unique', [['venue_connection_id', 'sku'], 'products_venue_sku_unique']);
        $this->replaceUniqueIndex('event_types', 'event_types_name_unique', [['venue_connection_id', 'name'], 'event_types_venue_name_unique']);
        $this->replaceUniqueIndex('halls', 'halls_slug_unique', [['venue_connection_id', 'slug'], 'halls_venue_slug_unique']);
    }

    public function down(): void
    {
        $this->dropUniqueIfExists('products', 'products_venue_sku_unique');
        $this->dropUniqueIfExists('event_types', 'event_types_venue_name_unique');
        $this->dropUniqueIfExists('halls', 'halls_venue_slug_unique');

        if (Schema::hasTable('products') && ! $this->indexExists('products', 'products_sku_unique')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->unique('sku');
            });
        }

        if (Schema::hasTable('event_types') && ! $this->indexExists('event_types', 'event_types_name_unique')) {
            Schema::table('event_types', function (Blueprint $table): void {
                $table->unique('name');
            });
        }

        if (Schema::hasTable('halls') && ! $this->indexExists('halls', 'halls_slug_unique')) {
            Schema::table('halls', function (Blueprint $table): void {
                $table->unique('slug');
            });
        }

        foreach (array_reverse($this->tenantTables) as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'venue_connection_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('venue_connection_id');
            });
        }
    }

    private function resolveLegacyVenueId(): ?int
    {
        $existingLegacyId = DB::table('venue_connections')
            ->where('username', 'legacy-tenant')
            ->value('id');

        if ($existingLegacyId) {
            return (int) $existingLegacyId;
        }

        $hasOperationalData = false;

        foreach ($this->tenantTables as $table) {
            if (Schema::hasTable($table) && DB::table($table)->exists()) {
                $hasOperationalData = true;
                break;
            }
        }

        if (! $hasOperationalData) {
            return DB::table('venue_connections')->value('id');
        }

        $settingName = Schema::hasTable('settings')
            ? DB::table('settings')->whereNull('user_id')->value('restaurant_name')
            : null;

        $now = now();

        return (int) DB::table('venue_connections')->insertGetId([
            'venue_name' => $settingName ?: 'Legacy Workspace',
            'owner_name' => 'Legacy tenant',
            'username' => 'legacy-tenant',
            'phone' => null,
            'message' => 'Auto-created during tenant isolation migration.',
            'status' => 'approved',
            'health_status' => 'healthy',
            'approved_at' => $now,
            'reviewed_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function replaceUniqueIndex(string $table, string $oldIndex, array $newDefinition): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $this->dropUniqueIfExists($table, $oldIndex);

        [$columns, $indexName] = $newDefinition;

        if (! $this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName): void {
                $blueprint->unique($columns, $indexName);
            });
        }
    }

    private function dropUniqueIfExists(string $table, string $indexName): void
    {
        if (! Schema::hasTable($table) || ! $this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($indexName): void {
            $blueprint->dropUnique($indexName);
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        $result = DB::selectOne(
            'select count(*) as aggregate from information_schema.statistics where table_schema = ? and table_name = ? and index_name = ?',
            [$database, $table, $indexName],
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }
};
