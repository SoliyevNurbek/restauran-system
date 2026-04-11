<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('venue_connections')) {
            Schema::table('venue_connections', function (Blueprint $table) {
                if (! Schema::hasColumn('venue_connections', 'telegram_chat_id')) {
                    $table->string('telegram_chat_id', 120)->nullable()->after('approval_notes');
                }

                if (! Schema::hasColumn('venue_connections', 'telegram_username')) {
                    $table->string('telegram_username', 120)->nullable()->after('telegram_chat_id');
                }

                if (! Schema::hasColumn('venue_connections', 'telegram_user_id')) {
                    $table->string('telegram_user_id', 120)->nullable()->after('telegram_username');
                }

                if (! Schema::hasColumn('venue_connections', 'telegram_linked_at')) {
                    $table->timestamp('telegram_linked_at')->nullable()->after('telegram_user_id');
                }
            });
        }

        if (Schema::hasTable('subscription_payments')) {
            Schema::table('subscription_payments', function (Blueprint $table) {
                if (! Schema::hasColumn('subscription_payments', 'proof_file_path')) {
                    $table->string('proof_file_path')->nullable()->after('description');
                }

                if (! Schema::hasColumn('subscription_payments', 'proof_note')) {
                    $table->text('proof_note')->nullable()->after('proof_file_path');
                }

                if (! Schema::hasColumn('subscription_payments', 'proof_received_at')) {
                    $table->timestamp('proof_received_at')->nullable()->after('proof_note');
                }

                if (! Schema::hasColumn('subscription_payments', 'rejection_reason')) {
                    $table->string('rejection_reason', 500)->nullable()->after('proof_received_at');
                }

                if (! Schema::hasColumn('subscription_payments', 'verified_by')) {
                    $table->foreignId('verified_by')->nullable()->after('rejection_reason')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('subscription_payments', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('verified_by');
                }

                if (! Schema::hasColumn('subscription_payments', 'telegram_chat_id')) {
                    $table->string('telegram_chat_id', 120)->nullable()->after('verified_at');
                }

                if (! Schema::hasColumn('subscription_payments', 'telegram_message_id')) {
                    $table->string('telegram_message_id', 120)->nullable()->after('telegram_chat_id');
                }

                if (! Schema::hasColumn('subscription_payments', 'internal_note')) {
                    $table->text('internal_note')->nullable()->after('telegram_message_id');
                }

                if (! Schema::hasColumn('subscription_payments', 'instruction_sent_at')) {
                    $table->timestamp('instruction_sent_at')->nullable()->after('internal_note');
                }
            });
        }

        if (! Schema::hasTable('telegram_messages')) {
            Schema::create('telegram_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subscription_payment_id')->nullable()->constrained('subscription_payments')->nullOnDelete();
                $table->foreignId('venue_connection_id')->nullable()->constrained()->nullOnDelete();
                $table->string('direction', 20);
                $table->string('chat_id', 120);
                $table->string('telegram_message_id', 120)->nullable();
                $table->string('message_type', 40);
                $table->text('content')->nullable();
                $table->string('file_path')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('telegram_messages')) {
            Schema::dropIfExists('telegram_messages');
        }

        if (Schema::hasTable('subscription_payments')) {
            Schema::table('subscription_payments', function (Blueprint $table) {
                if (Schema::hasColumn('subscription_payments', 'verified_by')) {
                    $table->dropConstrainedForeignId('verified_by');
                }

                foreach ([
                    'proof_file_path',
                    'proof_note',
                    'proof_received_at',
                    'rejection_reason',
                    'verified_at',
                    'telegram_chat_id',
                    'telegram_message_id',
                    'internal_note',
                    'instruction_sent_at',
                ] as $column) {
                    if (Schema::hasColumn('subscription_payments', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('venue_connections')) {
            Schema::table('venue_connections', function (Blueprint $table) {
                foreach ([
                    'telegram_chat_id',
                    'telegram_username',
                    'telegram_user_id',
                    'telegram_linked_at',
                ] as $column) {
                    if (Schema::hasColumn('venue_connections', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
