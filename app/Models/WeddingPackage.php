<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeddingPackage extends Model
{
    use HasFactory, BelongsToTenant;

    public const NAME_OPTIONS = [
        'Standart',
        'Premium',
        'Vip',
    ];

    protected $fillable = [
        'venue_connection_id',
        'name',
        'price_per_person',
        'description',
        'status',
        'image',
        'image_media_file_id',
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

    public function images(): HasMany
    {
        return $this->hasMany(WeddingPackageImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function imageMediaFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'image_media_file_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->imageMediaFile?->url();
    }
}
