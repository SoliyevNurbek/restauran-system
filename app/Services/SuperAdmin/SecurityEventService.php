<?php

namespace App\Services\SuperAdmin;

use App\Models\SecurityEvent;
use App\Models\User;
use App\Models\VenueConnection;
use Illuminate\Support\Facades\Schema;

class SecurityEventService
{
    public function record(
        string $eventType,
        string $title,
        ?string $description = null,
        ?User $user = null,
        ?VenueConnection $venue = null,
        string $severity = 'info',
        ?string $ip = null,
        ?string $userAgent = null,
        array $meta = [],
    ): SecurityEvent {
        if (! Schema::hasTable('security_events')) {
            return new SecurityEvent([
                'user_id' => $user?->getKey(),
                'venue_connection_id' => $venue?->getKey(),
                'event_type' => $eventType,
                'severity' => $severity,
                'title' => $title,
                'description' => $description,
                'ip' => $ip,
                'user_agent' => $userAgent ? mb_substr($userAgent, 0, 500) : null,
                'meta' => $meta,
                'occurred_at' => now(),
            ]);
        }

        return SecurityEvent::query()->create([
            'user_id' => $user?->getKey(),
            'venue_connection_id' => $venue?->getKey(),
            'event_type' => $eventType,
            'severity' => $severity,
            'title' => $title,
            'description' => $description,
            'ip' => $ip,
            'user_agent' => $userAgent ? mb_substr($userAgent, 0, 500) : null,
            'meta' => $meta,
            'occurred_at' => now(),
        ]);
    }
}
