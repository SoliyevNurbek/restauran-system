<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('remember_token');
            }

            if (! Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 64)->nullable()->after('last_login_at');
            }
        });

        Schema::table('venue_connections', function (Blueprint $table) {
            if (! Schema::hasColumn('venue_connections', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('approved_at');
            }

            if (! Schema::hasColumn('venue_connections', 'review_reason')) {
                $table->string('review_reason', 500)->nullable()->after('approval_notes');
            }
        });

        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('currency', 10)->default('UZS');
            $table->string('billing_cycle', 30)->default('monthly');
            $table->string('status', 30)->default('active');
            $table->unsignedInteger('display_order')->default(1);
            $table->json('features')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->string('type', 30)->default('manual');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('proof_required')->default(false);
            $table->unsignedInteger('display_order')->default(1);
            $table->json('config')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('business_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_connection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 30)->default('trial');
            $table->string('activity_state', 30)->default('healthy');
            $table->string('billing_cycle', 30)->default('monthly');
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('currency', 10)->default('UZS');
            $table->boolean('manual_override')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('renews_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('venue_connection_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('currency', 10)->default('UZS');
            $table->string('status', 30)->default('pending');
            $table->string('transaction_reference', 120)->nullable()->index();
            $table->string('invoice_number', 80)->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('integration_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();
        });

        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60);
            $table->string('title');
            $table->string('description', 500)->nullable();
            $table->string('icon', 60)->default('bell');
            $table->string('status', 30)->default('info');
            $table->string('action_url')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('occurred_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_label')->nullable();
            $table->string('severity', 30)->default('info');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('ip', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('venue_connection_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 60);
            $table->string('severity', 30)->default('info');
            $table->string('title');
            $table->string('description', 500)->nullable();
            $table->string('ip', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
        });

        DB::table('subscription_plans')->insert([
            [
                'name' => 'Start',
                'slug' => 'start',
                'description' => 'Yangi bizneslar uchun asosiy boshqaruv reja.',
                'amount' => 490000,
                'currency' => 'UZS',
                'billing_cycle' => 'monthly',
                'status' => 'active',
                'display_order' => 1,
                'features' => json_encode(['1 venue', 'Booking dashboard', 'Basic notifications']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Growth',
                'slug' => 'growth',
                'description' => 'Faol to‘yxonalar va restoranlar uchun kengaytirilgan reja.',
                'amount' => 990000,
                'currency' => 'UZS',
                'billing_cycle' => 'monthly',
                'status' => 'active',
                'display_order' => 2,
                'features' => json_encode(['Advanced analytics', 'Priority support', 'Multi-hall insights']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Katta operatorlar va premium SLA uchun.',
                'amount' => 2490000,
                'currency' => 'UZS',
                'billing_cycle' => 'monthly',
                'status' => 'active',
                'display_order' => 3,
                'features' => json_encode(['Dedicated onboarding', 'Custom workflows', 'Executive reporting']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('payment_methods')->insert([
            ['code' => 'cash', 'label' => 'Naqd / qo‘lda', 'type' => 'manual', 'is_enabled' => true, 'proof_required' => true, 'display_order' => 1, 'config' => json_encode(['placeholder' => 'Kassa yoki operator izohi']), 'notes' => 'Manual to‘lovlar uchun tasdiq dalili talab qilinadi.', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'card', 'label' => 'Karta', 'type' => 'online', 'is_enabled' => true, 'proof_required' => false, 'display_order' => 2, 'config' => json_encode(['placeholder' => 'Merchant ID']), 'notes' => null, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'bank_transfer', 'label' => 'Bank o‘tkazma', 'type' => 'manual', 'is_enabled' => true, 'proof_required' => true, 'display_order' => 3, 'config' => json_encode(['placeholder' => 'Hisob raqami']), 'notes' => null, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'click', 'label' => 'Click', 'type' => 'gateway', 'is_enabled' => true, 'proof_required' => false, 'display_order' => 4, 'config' => json_encode(['placeholder' => 'Service ID']), 'notes' => 'Gateway uchun tayyor placeholder.', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'payme', 'label' => 'Payme', 'type' => 'gateway', 'is_enabled' => true, 'proof_required' => false, 'display_order' => 5, 'config' => json_encode(['placeholder' => 'Merchant key']), 'notes' => 'Gateway uchun tayyor placeholder.', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'uzum', 'label' => 'Uzum', 'type' => 'gateway', 'is_enabled' => false, 'proof_required' => false, 'display_order' => 6, 'config' => json_encode(['placeholder' => 'Provider config']), 'notes' => 'Kelajakdagi integratsiya uchun.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('integration_settings');
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('business_subscriptions');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('subscription_plans');

        Schema::table('venue_connections', function (Blueprint $table) {
            if (Schema::hasColumn('venue_connections', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }

            if (Schema::hasColumn('venue_connections', 'review_reason')) {
                $table->dropColumn('review_reason');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }

            if (Schema::hasColumn('users', 'last_login_ip')) {
                $table->dropColumn('last_login_ip');
            }
        });
    }
};
