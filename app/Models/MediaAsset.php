<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class MediaAsset extends Model
{
    use HasFactory;

    protected static array $keyedCache = [];

    protected $fillable = [
        'key',
        'label',
        'disk',
        'path',
        'alt_text',
        'created_by',
        'owner_user_id',
        'media_file_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function mediaFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class);
    }

    public function url(): ?string
    {
        if ($this->mediaFile) {
            return $this->mediaFile->url();
        }

        if (! $this->path) {
            return null;
        }

        return null;
    }

    public static function keyed(?User $user = null): Collection
    {
        $cacheKey = $user ? 'user:'.$user->getKey() : 'global';

        if (isset(static::$keyedCache[$cacheKey])) {
            return static::$keyedCache[$cacheKey];
        }

        if (! Schema::hasTable('media_assets')) {
            return collect();
        }

        if (! $user) {
            return static::$keyedCache[$cacheKey] = static::query()
                ->with('mediaFile')
                ->whereNull('owner_user_id')
                ->get()
                ->keyBy('key');
        }

        return static::$keyedCache[$cacheKey] = static::query()
            ->with('mediaFile')
            ->where('owner_user_id', $user->getKey())
            ->get()
            ->keyBy('key');
    }

    public static function replace(
        string $key,
        UploadedFile $file,
        string $directory,
        ?int $userId = null,
        ?int $ownerUserId = null,
        ?string $label = null,
        ?string $altText = null,
    ): self {
        $asset = static::query()->firstOrNew([
            'key' => $key,
            'owner_user_id' => $ownerUserId,
        ]);

        $previousMediaFileId = $asset->media_file_id;
        $mediaFile = MediaFile::createFromUpload($file);

        $asset->fill([
            'label' => $label,
            'disk' => 'database',
            'path' => $mediaFile->filename ?: 'media-file-'.$mediaFile->getKey(),
            'alt_text' => $altText,
            'created_by' => $userId,
            'owner_user_id' => $ownerUserId,
            'media_file_id' => $mediaFile->getKey(),
        ]);
        $asset->save();

        if ($previousMediaFileId) {
            MediaFile::query()->whereKey($previousMediaFileId)->delete();
        }

        static::forgetKeyed($ownerUserId);

        return $asset;
    }

    public static function forgetKeyed(?int $userId = null): void
    {
        unset(static::$keyedCache[$userId === null ? 'global' : 'user:'.$userId]);
    }
}
