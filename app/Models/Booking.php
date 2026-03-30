<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'client_id',
        'hall_id',
        'event_type_id',
        'package_id',
        'event_date',
        'start_time',
        'end_time',
        'guest_count',
        'price_per_person',
        'total_amount',
        'advance_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'guest_count' => 'integer',
            'price_per_person' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'advance_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(WeddingPackage::class, 'package_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(BookingService::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function kitchenCosts(): HasMany
    {
        return $this->hasMany(KitchenCost::class);
    }

    public function eventCosts(): HasMany
    {
        return $this->hasMany(EventCost::class);
    }

    public function fixedCosts(): HasMany
    {
        return $this->hasMany(FixedCost::class);
    }

    public function getKitchenCostsTotalAttribute(): float
    {
        return (float) $this->kitchenCosts->sum(fn (KitchenCost $cost) => $cost->grand_total);
    }

    public function getEventCostsTotalAttribute(): float
    {
        return (float) $this->eventCosts->sum(fn (EventCost $cost) => $cost->grand_total);
    }

    public function getFixedCostsTotalAttribute(): float
    {
        return (float) $this->fixedCosts->sum(fn (FixedCost $cost) => $cost->grand_total);
    }

    public function getTotalCostsAttribute(): float
    {
        return $this->kitchen_costs_total + $this->event_costs_total + $this->fixed_costs_total;
    }

    public function getProfitAttribute(): float
    {
        return (float) $this->total_amount - $this->total_costs;
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('event_date', '>=', now()->toDateString())->orderBy('event_date')->orderBy('start_time');
    }

    public function scopeForDay($query, CarbonInterface|string $date)
    {
        return $query->whereDate('event_date', Carbon::parse($date)->toDateString());
    }
}
