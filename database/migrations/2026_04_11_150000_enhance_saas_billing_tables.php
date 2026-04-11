<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscription_plans')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                if (! Schema::hasColumn('subscription_plans', 'duration_days')) {
                    $table->unsignedInteger('duration_days')->default(30)->after('currency');
                }

                if (! Schema::hasColumn('subscription_plans', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('status');
                }
            });

            DB::table('subscription_plans')
                ->whereNull('duration_days')
                ->orWhere('duration_days', 0)
                ->update([
                    'duration_days' => DB::raw("
                        CASE billing_cycle
                            WHEN 'yearly' THEN 365
                            WHEN 'quarterly' THEN 90
                            WHEN 'monthly' THEN 30
                            ELSE 30
                        END
                    "),
                ]);

            DB::table('subscription_plans')
                ->where('slug', 'start')
                ->update(['name' => 'Basic', 'slug' => 'basic', 'display_order' => 1]);

            DB::table('subscription_plans')
                ->where('slug', 'growth')
                ->update(['name' => 'Pro', 'slug' => 'pro', 'display_order' => 2]);

            DB::table('subscription_plans')
                ->where('slug', 'enterprise')
                ->update(['name' => 'Premium', 'slug' => 'premium', 'display_order' => 3]);

            DB::table('subscription_plans')->update([
                'is_active' => DB::raw("CASE WHEN status = 'active' THEN 1 ELSE 0 END"),
            ]);
        }

        if (Schema::hasTable('business_subscriptions')) {
            Schema::table('business_subscriptions', function (Blueprint $table) {
                if (! Schema::hasColumn('business_subscriptions', 'auto_renew')) {
                    $table->boolean('auto_renew')->default(false)->after('manual_override');
                }

                if (! Schema::hasColumn('business_subscriptions', 'source_payment_id')) {
                    $table->foreignId('source_payment_id')->nullable()->after('user_id')->constrained('subscription_payments')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('subscription_payments')) {
            Schema::table('subscription_payments', function (Blueprint $table) {
                if (! Schema::hasColumn('subscription_payments', 'subscription_plan_id')) {
                    $table->foreignId('subscription_plan_id')->nullable()->after('business_subscription_id')->constrained('subscription_plans')->nullOnDelete();
                }

                if (! Schema::hasColumn('subscription_payments', 'provider')) {
                    $table->string('provider', 30)->default('manual')->after('payment_method_id');
                }

                if (! Schema::hasColumn('subscription_payments', 'method')) {
                    $table->string('method', 40)->nullable()->after('provider');
                }

                if (! Schema::hasColumn('subscription_payments', 'provider_payment_id')) {
                    $table->string('provider_payment_id', 120)->nullable()->after('transaction_reference')->index();
                }

                if (! Schema::hasColumn('subscription_payments', 'external_transaction_id')) {
                    $table->string('external_transaction_id', 120)->nullable()->after('provider_payment_id')->index();
                }

                if (! Schema::hasColumn('subscription_payments', 'payment_for')) {
                    $table->string('payment_for', 30)->default('subscription')->after('status');
                }

                if (! Schema::hasColumn('subscription_payments', 'description')) {
                    $table->string('description', 500)->nullable()->after('invoice_number');
                }
            });

            DB::table('subscription_payments')
                ->whereNull('method')
                ->update(['method' => DB::raw("provider")]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('subscription_payments')) {
            Schema::table('subscription_payments', function (Blueprint $table) {
                foreach (['subscription_plan_id', 'provider', 'method', 'provider_payment_id', 'external_transaction_id', 'payment_for', 'description'] as $column) {
                    if (Schema::hasColumn('subscription_payments', $column)) {
                        if ($column === 'subscription_plan_id') {
                            $table->dropConstrainedForeignId($column);
                        } else {
                            $table->dropColumn($column);
                        }
                    }
                }
            });
        }

        if (Schema::hasTable('business_subscriptions')) {
            Schema::table('business_subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('business_subscriptions', 'source_payment_id')) {
                    $table->dropConstrainedForeignId('source_payment_id');
                }

                if (Schema::hasColumn('business_subscriptions', 'auto_renew')) {
                    $table->dropColumn('auto_renew');
                }
            });
        }

        if (Schema::hasTable('subscription_plans')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                if (Schema::hasColumn('subscription_plans', 'duration_days')) {
                    $table->dropColumn('duration_days');
                }

                if (Schema::hasColumn('subscription_plans', 'is_active')) {
                    $table->dropColumn('is_active');
                }
            });
        }
    }
};
