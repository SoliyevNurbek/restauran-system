<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'label',
        'type',
        'is_enabled',
        'proof_required',
        'display_order',
        'config',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'proof_required' => 'boolean',
            'config' => 'array',
        ];
    }

    public function subscriptionPayments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }
}
