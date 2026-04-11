<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VenueConnection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationApprovalFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_keeps_password_through_superadmin_approval(): void
    {
        if (config('database.default') === 'sqlite' && config('database.connections.sqlite.database') === ':memory:') {
            $this->markTestSkipped('Registration approval flow test requires the configured application database, not in-memory sqlite.');
        }

        $username = 'pending_owner_'.fake()->unique()->numerify('####');
        $password = 'OwnerPass1!';

        $this->get(route('register', ['lang' => 'uz']))
            ->assertOk()
            ->assertSee('name="password"', false)
            ->assertSee('name="password_confirmation"', false);

        $this->post(route('register.store', ['lang' => 'uz']), [
            'first_name' => 'Ali',
            'last_name' => 'Valiyev',
            'username' => $username,
            'phone' => '+998901112233',
            'restaurant_name' => 'Flow Test Venue',
            'message' => 'Flow registration test',
            'password' => $password,
            'password_confirmation' => $password,
            'terms' => '1',
        ])->assertRedirect(route('login', ['lang' => 'uz']));

        $venue = VenueConnection::query()->where('username', $username)->first();
        $this->assertNotNull($venue);
        $this->assertSame('pending', $venue->status);

        $user = User::query()->where('username', $username)->first();
        $this->assertNotNull($user);
        $this->assertSame('pending', $user->status);
        $this->assertSame('admin', $user->role);
        $this->assertSame($venue->id, $user->venue_connection_id);
        $this->assertTrue(Hash::check($password, $user->password));

        $originalHash = $user->password;

        $this->post(route('login.store', ['lang' => 'uz']), [
            'username' => $username,
            'password' => $password,
        ])->assertSessionHasErrors('username');

        $superadmin = User::factory()->create([
            'username' => 'superadmin_flow_'.fake()->unique()->numerify('####'),
            'password' => Hash::make('SuperAdmin1!'),
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        $this->actingAs($superadmin)
            ->put(route('superadmin.approvals.update', $venue), [
                'status' => 'approved',
                'approval_notes' => 'Approved during feature test',
                'review_reason' => 'Valid business data',
                'send_telegram' => false,
            ])->assertRedirect();

        $venue->refresh();
        $user->refresh();

        $this->assertSame('approved', $venue->status);
        $this->assertSame('active', $user->status);
        $this->assertSame($originalHash, $user->password);
        $this->assertSame($user->id, $venue->admin_user_id);

        $this->post(route('logout'))->assertRedirect(route('landing'));

        $this->post(route('login.store', ['lang' => 'uz']), [
            'username' => $username,
            'password' => $password,
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user->fresh());
    }
}
