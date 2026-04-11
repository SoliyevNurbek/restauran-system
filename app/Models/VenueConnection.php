<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VenueConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_name',
        'owner_name',
        'username',
        'email',
        'phone',
        'message',
        'status',
        'health_status',
        'halls_count',
        'bookings_count',
        'revenue_total',
        'approved_at',
        'last_seen_at',
        'approved_by',
        'admin_user_id',
        'approval_notes',
        'reviewed_at',
        'review_reason',
        'is_system_workspace',
        'telegram_chat_id',
        'telegram_username',
        'telegram_user_id',
        'telegram_linked_at',
        'telegram_verified_at',
        'telegram_link_token',
        'telegram_notifications_enabled',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'telegram_linked_at' => 'datetime',
            'telegram_verified_at' => 'datetime',
            'revenue_total' => 'decimal:2',
            'is_system_workspace' => 'boolean',
            'telegram_notifications_enabled' => 'boolean',
        ];
    }

    public function scopeVisibleToSuperadmin(Builder $query): Builder
    {
        return $query->where('is_system_workspace', false);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function pendingUser(): HasOne
    {
        return $this->hasOne(User::class, 'venue_connection_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BusinessSubscription::class);
    }

    public function latestSubscription(): HasOne
    {
        return $this->hasOne(BusinessSubscription::class)->latestOfMany();
    }

    public function subscriptionPayments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function telegramMessages(): HasMany
    {
        return $this->hasMany(TelegramMessage::class);
    }

    public function securityEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class);
    }
}
