<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VenueConnection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SystemWorkspaceVisibilityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_system_workspace_is_hidden_from_superadmin_business_modules(): void
    {
        if (config('database.default') === 'sqlite' && config('database.connections.sqlite.database') === ':memory:') {
            $this->markTestSkipped('System workspace visibility test requires the configured application database, not in-memory sqlite.');
        }

        $superadmin = User::factory()->create([
            'username' => 'superadmin_hidden_'.fake()->unique()->numerify('####'),
            'password' => Hash::make('SuperAdmin1!'),
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        $systemWorkspace = VenueConnection::query()->create([
            'venue_name' => 'Legacy Workspace',
            'owner_name' => 'System',
            'username' => 'legacy-hidden-'.fake()->unique()->numerify('####'),
            'status' => 'approved',
            'health_status' => 'healthy',
            'is_system_workspace' => true,
        ]);

        $clientWorkspace = VenueConnection::query()->create([
            'venue_name' => 'Client Venue',
            'owner_name' => 'Client Owner',
            'username' => 'client-visible-'.fake()->unique()->numerify('####'),
            'status' => 'pending',
            'health_status' => 'new',
            'is_system_workspace' => false,
        ]);

        $this->actingAs($superadmin);

        $this->get(route('superadmin.businesses.index'))
            ->assertOk()
            ->assertSee('Client Venue')
            ->assertDontSee('Legacy Workspace');

        $this->get(route('superadmin.approvals.index'))
            ->assertOk()
            ->assertSee('Client Venue')
            ->assertDontSee('Legacy Workspace');

        $this->get(route('superadmin.businesses.show', $systemWorkspace))
            ->assertNotFound();

        $this->get(route('superadmin.businesses.show', $clientWorkspace))
            ->assertOk()
            ->assertSee('Client Venue');
    }
}
