<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class Page extends Model
{
    use HasFactory;

    public const TERMS_OF_USE = 'terms-of-use';
    public const PRIVACY_POLICY = 'privacy-policy';

    public const ALLOWED_SLUGS = [
        self::TERMS_OF_USE,
        self::PRIVACY_POLICY,
    ];

    protected $fillable = [
        'slug',
        'locale',
        'title',
        'content',
        'version',
        'published_at',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeForSlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeForLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public static function allowedSlugs(): array
    {
        return self::ALLOWED_SLUGS;
    }

    public static function currentPublished(string $slug, string $locale = 'uz'): ?self
    {
        if (! Schema::hasTable('pages') || ! in_array($slug, self::ALLOWED_SLUGS, true)) {
            return null;
        }

        return self::query()
            ->forSlug($slug)
            ->forLocale('uz')
            ->published()
            ->orderByDesc('version')
            ->first();
    }

    public static function latestVersionNumber(string $slug, string $locale = 'uz'): int
    {
        if (! Schema::hasTable('pages') || ! in_array($slug, self::ALLOWED_SLUGS, true)) {
            return 0;
        }

        return (int) self::query()
            ->forSlug($slug)
            ->forLocale('uz')
            ->max('version');
    }

    public static function historyForSlug(string $slug, string $locale = 'uz', int $limit = 10): Collection
    {
        if (! Schema::hasTable('pages') || ! in_array($slug, self::ALLOWED_SLUGS, true)) {
            return collect();
        }

        return self::query()
            ->forSlug($slug)
            ->forLocale('uz')
            ->with('editor:id,name,username')
            ->orderByDesc('version')
            ->limit($limit)
            ->get();
    }
}
