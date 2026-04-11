<?php

namespace Tests\Feature;

use App\Models\AdminNotification;
use App\Models\BusinessSubscription;
use App\Models\LandingContent;
use App\Models\Page;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\VenueConnection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperadminSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_superadmin_panel_pages_and_actions_work(): void
    {
        if (config('database.default') === 'sqlite' && config('database.connections.sqlite.database') === ':memory:') {
            $this->markTestSkipped('Superadmin smoke test requires the configured application database, not in-memory sqlite.');
        }

        $user = User::query()->where('role', 'superadmin')->first();
        $this->assertNotNull($user, 'Superadmin user not found.');

        $plainPassword = 'TempPass1!';
        $user->forceFill(['password' => Hash::make($plainPassword)])->save();

        $venue = VenueConnection::query()->create([
            'venue_name' => 'Smoke Venue '.uniqid(),
            'owner_name' => 'Smoke Owner',
            'username' => 'smoke_'.uniqid(),
            'phone' => '+998900000001',
            'status' => 'pending',
        ]);

        $managedUser = User::query()->create([
            'name' => 'Smoke Admin',
            'username' => 'smoke_admin_'.uniqid(),
            'password' => 'TempPass1!',
            'role' => 'admin',
            'status' => 'active',
            'venue_connection_id' => $venue->id,
        ]);

        $plan = SubscriptionPlan::query()->first();
        $this->assertNotNull($plan, 'Subscription plan not found.');

        $method = PaymentMethod::query()->first();
        $this->assertNotNull($method, 'Payment method not found.');

        $subscription = BusinessSubscription::query()->create([
            'venue_connection_id' => $venue->id,
            'user_id' => $managedUser->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'activity_state' => 'healthy',
            'billing_cycle' => 'monthly',
            'amount' => $plan->amount,
            'currency' => $plan->currency,
            'manual_override' => false,
            'starts_at' => now()->subDay(),
            'renews_at' => now()->addMonth(),
            'expires_at' => now()->addMonth(),
            'notes' => 'Smoke subscription',
        ]);

        $payment = SubscriptionPayment::query()->create([
            'business_subscription_id' => $subscription->id,
            'venue_connection_id' => $venue->id,
            'user_id' => $managedUser->id,
            'payment_method_id' => $method->id,
            'amount' => $subscription->amount,
            'currency' => $subscription->currency,
            'status' => 'pending',
            'transaction_reference' => 'SMOKE-REF',
            'invoice_number' => 'SMOKE-INV',
            'due_date' => now()->addWeek(),
            'notes' => 'Smoke payment',
        ]);

        $notification = AdminNotification::query()->create([
            'type' => 'payment_received',
            'title' => 'Smoke notification',
            'description' => 'Smoke notification body',
            'status' => 'info',
            'icon' => 'bell',
            'is_read' => false,
            'occurred_at' => now(),
        ]);

        Setting::global();

        $page = Page::query()->updateOrCreate(
            [
                'slug' => Page::TERMS_OF_USE,
                'locale' => 'uz',
                'version' => 1,
            ],
            [
                'title' => 'Smoke Terms',
                'content' => 'Smoke content',
                'published_at' => now()->subMinute(),
                'updated_by' => $user->id,
            ],
        );

        LandingContent::query()->updateOrCreate(
            [
                'locale' => 'uz',
            ],
            [
                'hero_title' => 'Smoke landing',
            ],
        );

        $this->actingAs($user);

        foreach ([
            route('superadmin.dashboard'),
            route('superadmin.analytics.index'),
            route('superadmin.approvals.index'),
            route('superadmin.businesses.index'),
            route('superadmin.businesses.show', $venue),
            route('superadmin.users.index'),
            route('superadmin.users.show', $managedUser),
            route('superadmin.subscriptions.index'),
            route('superadmin.payments.index'),
            route('superadmin.payments.show', $payment),
            route('superadmin.notifications.index'),
            route('superadmin.audit.index'),
            route('superadmin.integrations.edit'),
            route('superadmin.security.index'),
            route('superadmin.settings.edit'),
            route('superadmin.pages.edit', ['slug' => Page::TERMS_OF_USE]),
            route('superadmin.languages.edit', ['lang' => 'uz']),
            route('superadmin.landing.edit', ['lang' => 'uz']),
            route('superadmin.venues.index'),
        ] as $url) {
            $this->get($url)->assertOk();
        }

        $this->put(route('superadmin.approvals.update', $venue), [
            'status' => 'under_review',
            'approval_notes' => 'Smoke approval note',
            'review_reason' => 'Smoke approval reason',
            'send_telegram' => false,
        ])->assertRedirect();

        $this->put(route('superadmin.businesses.update', $venue), [
            'status' => 'approved',
            'approval_notes' => 'Smoke business note',
            'review_reason' => 'Smoke business reason',
            'health_status' => 'healthy',
            'halls_count' => 2,
            'bookings_count' => 5,
            'revenue_total' => 250000,
            'send_telegram' => false,
        ])->assertRedirect();

        $this->put(route('superadmin.users.update', $managedUser), [
            'role' => 'admin',
            'status' => 'active',
        ])->assertRedirect();

        $this->put(route('superadmin.subscriptions.update', $subscription), [
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'activity_state' => 'healthy',
            'billing_cycle' => 'monthly',
            'amount' => $subscription->amount,
            'currency' => $subscription->currency,
            'manual_override' => false,
            'renews_at' => optional($subscription->renews_at)->format('Y-m-d'),
            'expires_at' => optional($subscription->expires_at)->format('Y-m-d'),
            'trial_ends_at' => optional($subscription->trial_ends_at)->format('Y-m-d'),
            'notes' => 'Smoke subscription update',
        ])->assertRedirect();

        $this->put(route('superadmin.payments.update', $payment), [
            'payment_method_id' => $method->id,
            'status' => 'paid',
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'transaction_reference' => 'SMOKE-REF-UPDATED',
            'invoice_number' => $payment->invoice_number,
            'paid_at' => now()->format('Y-m-d H:i:s'),
            'due_date' => optional($payment->due_date)->format('Y-m-d H:i:s'),
            'notes' => 'Smoke payment update',
        ])->assertRedirect();

        $this->put(route('superadmin.payment-methods.update', $method), [
            'label' => $method->label,
            'type' => $method->type,
            'is_enabled' => $method->is_enabled,
            'proof_required' => $method->proof_required,
            'display_order' => $method->display_order,
            'config_placeholder' => 'smoke-config',
            'notes' => 'Smoke method update',
        ])->assertRedirect();

        $this->put(route('superadmin.notifications.update', $notification), [
            'is_read' => true,
        ])->assertRedirect();

        $this->put(route('superadmin.integrations.telegram.update'), [
            'bot_token' => '',
            'chat_id' => '',
            'alerts' => ['payment_received', 'payment_failed'],
        ])->assertRedirect();

        $this->post(route('superadmin.integrations.telegram.test'))->assertRedirect();

        $this->put(route('superadmin.settings.update'), [
            'restaurant_name' => 'MyRestaurant_SN',
            'contact_phone' => '+998900000002',
        ])->assertRedirect();

        $this->put(route('superadmin.settings.password.update'), [
            'current_password' => $plainPassword,
            'password' => 'NewTempPass1!',
            'password_confirmation' => 'NewTempPass1!',
        ])->assertRedirect();

        $this->put(route('superadmin.pages.update'), [
            'slug' => $page->slug,
            'title' => $page->title,
            'content' => $page->content,
            'published_at' => optional($page->published_at)->format('Y-m-d\TH:i'),
        ])->assertRedirect();

        $this->put(route('superadmin.languages.update'), [
            'locale' => 'uz',
            'lines' => ['brand_tagline' => 'Smoke tagline'],
        ])->assertRedirect();

        $this->put(route('superadmin.landing.update'), [
            'locale' => 'uz',
            'hero_badge' => 'Smoke badge',
            'hero_title' => 'Smoke title',
            'hero_text' => 'Smoke text',
            'hero_primary_cta' => 'Boshlash',
            'hero_secondary_cta' => 'Demo',
            'hero_microcopy' => 'Smoke microcopy',
            'final_title' => 'Smoke final',
            'final_text' => 'Smoke final text',
            'contact_title' => 'Smoke contact',
            'contact_text' => 'Smoke contact text',
        ])->assertRedirect();

        $this->put(route('superadmin.venues.update', $venue), [
            'status' => 'approved',
            'approval_notes' => 'Legacy venue note',
            'review_reason' => 'Legacy reason',
            'health_status' => 'healthy',
            'halls_count' => 4,
            'bookings_count' => 8,
            'revenue_total' => 300000,
        ])->assertRedirect();

        $this->post(route('superadmin.venues.reset-credentials', $venue))
            ->assertRedirect();
    }
}
