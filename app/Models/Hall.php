<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Hall extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'venue_connection_id',
        'name',
        'slug',
        'capacity',
        'price',
        'status',
        'description',
        'image',
        'image_media_file_id',
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

    public function imageMediaFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'image_media_file_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->imageMediaFile?->url();
    }
}
