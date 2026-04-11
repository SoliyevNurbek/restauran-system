<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenCost extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'venue_connection_id',
        'booking_id',
        'category_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
        'gas_cost',
        'electric_cost',
        'salary_cost',
        'tax_share',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'gas_cost' => 'decimal:2',
            'electric_cost' => 'decimal:2',
            'salary_cost' => 'decimal:2',
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
        return (float) $this->total_price + (float) $this->gas_cost + (float) $this->electric_cost + (float) $this->salary_cost + (float) $this->tax_share;
    }
}
