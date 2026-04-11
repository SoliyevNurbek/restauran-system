<?php

namespace App\Support;

use App\Models\User;

class TenantContext
{
    public static function user(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    public static function id(): ?int
    {
        $user = static::user();

        if (! $user || $user->isSuperAdmin()) {
            return null;
        }

        return $user->venue_connection_id ? (int) $user->venue_connection_id : null;
    }

    public static function scoped(): bool
    {
        return static::user() !== null && ! static::user()?->isSuperAdmin();
    }
}
