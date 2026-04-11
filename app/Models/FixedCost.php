<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedCost extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'venue_connection_id',
        'booking_id',
        'name',
        'monthly_amount',
        'allocated_amount',
        'tax_share',
    ];

    protected function casts(): array
    {
        return [
            'monthly_amount' => 'decimal:2',
            'allocated_amount' => 'decimal:2',
            'tax_share' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getGrandTotalAttribute(): float
    {
        return (float) $this->allocated_amount + (float) $this->tax_share;
    }
}
