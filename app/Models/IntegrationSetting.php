<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

class IntegrationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'is_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
        ];
    }

    public function getResolvedValueAttribute(): ?string
    {
        if ($this->value === null) {
            return null;
        }

        if (! $this->is_encrypted) {
            return $this->value;
        }

        try {
            return Crypt::decryptString($this->value);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function putValue(string $key, ?string $value, bool $encrypt = false): self
    {
        if (! Schema::hasTable('integration_settings')) {
            return new self([
                'key' => $key,
                'value' => $value,
                'is_encrypted' => $encrypt,
            ]);
        }

        return static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $value === null ? null : ($encrypt ? Crypt::encryptString($value) : $value),
                'is_encrypted' => $encrypt,
            ],
        );
    }

    public static function valueFor(string $key): ?string
    {
        if (! Schema::hasTable('integration_settings')) {
            return null;
        }

        return static::query()->where('key', $key)->first()?->resolved_value;
    }
}
