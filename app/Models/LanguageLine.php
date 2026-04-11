<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class LanguageLine extends Model
{
    use HasFactory;

    protected static ?Collection $groupedCache = null;

    protected $fillable = [
        'locale',
        'key',
        'value',
    ];

    public static function allGrouped(): Collection
    {
        if (static::$groupedCache) {
            return static::$groupedCache;
        }

        if (! Schema::hasTable('language_lines')) {
            return collect();
        }

        return static::$groupedCache = Cache::remember('language_lines.grouped', now()->addMinutes(30), fn () => static::query()
            ->orderBy('locale')
            ->orderBy('key')
            ->get()
            ->groupBy('locale')
            ->map(fn (Collection $lines) => $lines->pluck('value', 'key')));
    }

    public static function flushGroupedCache(): void
    {
        static::$groupedCache = null;
        Cache::forget('language_lines.grouped');
    }
}
