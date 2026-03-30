<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeddingPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price_per_person',
        'description',
        'status',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'price_per_person' => 'decimal:2',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'package_id');
    }
}
