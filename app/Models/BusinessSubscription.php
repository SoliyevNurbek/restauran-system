<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_connection_id',
        'user_id',
        'source_payment_id',
        'subscription_plan_id',
        'status',
        'activity_state',
        'billing_cycle',
        'amount',
        'currency',
        'manual_override',
        'auto_renew',
        'starts_at',
        'trial_ends_at',
        'renews_at',
        'expires_at',
        'canceled_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'manual_override' => 'boolean',
            'auto_renew' => 'boolean',
            'starts_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'renews_at' => 'datetime',
            'expires_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    public function venueConnection(): BelongsTo
    {
        return $this->belongsTo(VenueConnection::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function sourcePayment(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPayment::class, 'source_payment_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function getCurrentPeriodEndAttribute()
    {
        return $this->renews_at ?? $this->expires_at ?? $this->trial_ends_at;
    }
}
