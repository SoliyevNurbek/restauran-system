<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'category_id',
        'service_name',
        'quantity',
        'unit_price',
        'total_price',
        'salary_cost',
        'utility_cost',
        'tax_share',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'salary_cost' => 'decimal:2',
            'utility_cost' => 'decimal:2',
            'tax_share' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CostCategory::class, 'category_id');
    }

    public function getGrandTotalAttribute(): float
    {
        return (float) $this->total_price + (float) $this->salary_cost + (float) $this->utility_cost + (float) $this->tax_share;
    }
}
