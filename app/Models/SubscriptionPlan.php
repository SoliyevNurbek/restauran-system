<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'amount',
        'currency',
        'duration_days',
        'billing_cycle',
        'status',
        'is_active',
        'display_order',
        'features',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'duration_days' => 'integer',
            'is_active' => 'boolean',
            'features' => 'array',
        ];
    }

    public function getPriceAttribute(): float
    {
        return (float) $this->amount;
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BusinessSubscription::class);
    }
}
