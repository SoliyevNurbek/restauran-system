<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected static array $resolvedSettings = [];

    protected $fillable = [
        'restaurant_name',
        'contact_phone',
        'logo_path',
        'favicon_path',
        'logo_media_file_id',
        'favicon_media_file_id',
        'theme_preference',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logoMediaFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'logo_media_file_id');
    }

    public function faviconMediaFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'favicon_media_file_id');
    }

    public static function global(): self
    {
        if (isset(static::$resolvedSettings['global'])) {
            return static::$resolvedSettings['global'];
        }

        $setting = static::with(['logoMediaFile', 'faviconMediaFile'])->firstOrCreate(['user_id' => null], [
            'restaurant_name' => 'MyRestoran',
            'contact_phone' => null,
            'theme_preference' => 'light',
        ]);

        return static::$resolvedSettings['global'] = $setting;
    }

    public static function currentFor(?User $user): self
    {
        if (! $user) {
            return static::global();
        }

        $cacheKey = 'user:'.$user->getKey();

        if (isset(static::$resolvedSettings[$cacheKey])) {
            return static::$resolvedSettings[$cacheKey];
        }

        $setting = static::with(['logoMediaFile', 'faviconMediaFile'])->firstOrCreate(['user_id' => $user->getKey()], [
            'restaurant_name' => '',
            'contact_phone' => null,
            'theme_preference' => 'light',
        ]);

        return static::$resolvedSettings[$cacheKey] = $setting;
    }

    public function logoUrl(): ?string
    {
        return $this->logoMediaFile?->url();
    }

    public function faviconUrl(): ?string
    {
        return $this->faviconMediaFile?->url();
    }

    public static function forgetResolved(?int $userId = null): void
    {
        if ($userId === null) {
            unset(static::$resolvedSettings['global']);

            return;
        }

        unset(static::$resolvedSettings['user:'.$userId]);
    }
}
