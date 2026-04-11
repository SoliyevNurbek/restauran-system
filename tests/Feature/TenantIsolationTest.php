<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\VenueConnection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_business_users_only_see_their_own_tenant_records(): void
    {
        if (config('database.default') === 'sqlite' && config('database.connections.sqlite.database') === ':memory:') {
            $this->markTestSkipped('Tenant isolation test requires the configured application database, not in-memory sqlite.');
        }

        $venueA = VenueConnection::query()->create([
            'venue_name' => 'Tenant A Venue',
            'owner_name' => 'Owner A',
            'username' => 'tenant_a_'.fake()->unique()->numerify('####'),
            'status' => 'approved',
            'health_status' => 'healthy',
        ]);

        $venueB = VenueConnection::query()->create([
            'venue_name' => 'Tenant B Venue',
            'owner_name' => 'Owner B',
            'username' => 'tenant_b_'.fake()->unique()->numerify('####'),
            'status' => 'approved',
            'health_status' => 'healthy',
        ]);

        $userA = User::factory()->create([
            'username' => 'tenant_user_a_'.fake()->unique()->numerify('####'),
            'password' => Hash::make('TenantPass1!'),
            'role' => 'admin',
            'status' => 'active',
            'venue_connection_id' => $venueA->id,
        ]);

        $userB = User::factory()->create([
            'username' => 'tenant_user_b_'.fake()->unique()->numerify('####'),
            'password' => Hash::make('TenantPass1!'),
            'role' => 'admin',
            'status' => 'active',
            'venue_connection_id' => $venueB->id,
        ]);

        $productA = Product::query()->create([
            'venue_connection_id' => $venueA->id,
            'category' => 'Oziq-ovqat',
            'subcategory' => 'Go`sht',
            'name' => 'Tenant A Product',
            'unit' => 'kg',
            'sku' => 'TENANT-A-'.fake()->unique()->numerify('####'),
            'current_stock' => 10,
            'minimum_stock' => 2,
            'last_purchase_price' => 10000,
            'is_active' => true,
        ]);

        $productB = Product::query()->create([
            'venue_connection_id' => $venueB->id,
            'category' => 'Oziq-ovqat',
            'subcategory' => 'Go`sht',
            'name' => 'Tenant B Product',
            'unit' => 'kg',
            'sku' => 'TENANT-B-'.fake()->unique()->numerify('####'),
            'current_stock' => 8,
            'minimum_stock' => 1,
            'last_purchase_price' => 11000,
            'is_active' => true,
        ]);

        $this->actingAs($userA);

        $this->assertSame(1, Product::query()->count());
        $this->assertSame($productA->id, Product::query()->firstOrFail()->id);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Tenant A Product')
            ->assertDontSee('Tenant B Product');

        $this->get(route('products.edit', $productB))
            ->assertNotFound();

        auth()->logout();
        $this->actingAs($userB);

        $this->assertSame(1, Product::query()->count());
        $this->assertSame($productB->id, Product::query()->firstOrFail()->id);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Tenant B Product')
            ->assertDontSee('Tenant A Product');

        $this->get(route('products.edit', $productA))
            ->assertNotFound();
    }
}
