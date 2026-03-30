<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Hall extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'capacity',
        'price',
        'status',
        'description',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Hall $hall) {
            if (blank($hall->slug)) {
                $hall->slug = Str::slug($hall->name);
            }
        });
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
