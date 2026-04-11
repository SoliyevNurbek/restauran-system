<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_subscription_id',
        'subscription_plan_id',
        'venue_connection_id',
        'user_id',
        'payment_method_id',
        'provider',
        'method',
        'amount',
        'currency',
        'status',
        'transaction_reference',
        'provider_payment_id',
        'external_transaction_id',
        'invoice_number',
        'payment_for',
        'description',
        'proof_file_path',
        'proof_note',
        'proof_received_at',
        'rejection_reason',
        'verified_by',
        'verified_at',
        'telegram_chat_id',
        'telegram_message_id',
        'internal_note',
        'instruction_sent_at',
        'paid_at',
        'due_date',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'proof_received_at' => 'datetime',
            'verified_at' => 'datetime',
            'instruction_sent_at' => 'datetime',
            'paid_at' => 'datetime',
            'due_date' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(BusinessSubscription::class, 'business_subscription_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function venueConnection(): BelongsTo
    {
        return $this->belongsTo(VenueConnection::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function telegramMessages(): HasMany
    {
        return $this->hasMany(TelegramMessage::class, 'subscription_payment_id');
    }

    public function getDisplayStatusAttribute(): string
    {
        return str($this->status)->headline()->toString();
    }
}
