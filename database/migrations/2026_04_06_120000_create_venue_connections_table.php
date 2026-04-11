<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_connections', function (Blueprint $table) {
            $table->id();
            $table->string('venue_name');
            $table->string('owner_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('pending');
            $table->string('health_status')->default('new');
            $table->unsignedInteger('halls_count')->default(0);
            $table->unsignedInteger('bookings_count')->default(0);
            $table->decimal('revenue_total', 14, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('approval_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_connections');
    }
};
