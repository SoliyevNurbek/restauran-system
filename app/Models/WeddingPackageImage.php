<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeddingPackageImage extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'venue_connection_id',
        'wedding_package_id',
        'image_path',
        'media_file_id',
        'sort_order',
    ];

    public function weddingPackage(): BelongsTo
    {
        return $this->belongsTo(WeddingPackage::class);
    }

    public function mediaFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class);
    }

    public function url(): ?string
    {
        return $this->mediaFile?->url();
    }
}
