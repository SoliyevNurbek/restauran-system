<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'subcategory',
        'name',
        'unit',
        'sku',
        'received_quantity',
        'minimum_stock',
        'current_stock',
        'last_purchase_price',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'received_quantity' => 'decimal:3',
            'minimum_stock' => 'decimal:3',
            'current_stock' => 'decimal:3',
            'last_purchase_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public const UNIT_OPTIONS = [
        'kg',
        'dona',
        'soni',
        'blok',
        'bog\'',
        'litr',
        'paket',
        'banka',
        'rulon',
        'komplekt',
        'nafar',
        'xizmat',
    ];

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function bookingUsageItems(): HasMany
    {
        return $this->hasMany(BookingUsageItem::class);
    }
}
