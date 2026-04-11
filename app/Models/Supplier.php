<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'venue_connection_id',
        'full_name',
        'phone',
        'company_name',
        'opening_balance',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function getBalanceAttribute(): float
    {
        $purchaseTotal = $this->purchases_sum_total_amount
            ?? $this->purchases->sum('total_amount')
            ?? 0;

        $paymentTotal = $this->payments_sum_amount
            ?? $this->payments->sum('amount')
            ?? 0;

        return (float) $this->opening_balance + (float) $purchaseTotal - (float) $paymentTotal;
    }
}
