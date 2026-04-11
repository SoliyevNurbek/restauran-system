<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_payment_id',
        'venue_connection_id',
        'direction',
        'chat_id',
        'telegram_message_id',
        'message_type',
        'content',
        'file_path',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPayment::class, 'subscription_payment_id');
    }

    public function venueConnection(): BelongsTo
    {
        return $this->belongsTo(VenueConnection::class);
    }
}
