<?php

namespace App\Services\SuperAdmin;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AuditLogService
{
    public function record(
        string $action,
        ?Model $target = null,
        ?array $before = null,
        ?array $after = null,
        string $severity = 'info',
        ?Request $request = null,
        ?string $targetLabel = null,
    ): AuditLog {
        if (! Schema::hasTable('audit_logs')) {
            return new AuditLog([
                'actor_id' => $request?->user()?->getKey(),
                'action' => $action,
                'target_type' => $target ? $target::class : null,
                'target_id' => $target?->getKey(),
                'target_label' => $targetLabel ?? $target?->getAttribute('name') ?? $target?->getAttribute('venue_name'),
                'severity' => $severity,
                'before' => $before,
                'after' => $after,
                'ip' => $request?->ip(),
                'user_agent' => $request?->userAgent() ? mb_substr($request->userAgent(), 0, 500) : null,
            ]);
        }

        return AuditLog::query()->create([
            'actor_id' => $request?->user()?->getKey(),
            'action' => $action,
            'target_type' => $target ? $target::class : null,
            'target_id' => $target?->getKey(),
            'target_label' => $targetLabel ?? $target?->getAttribute('name') ?? $target?->getAttribute('venue_name'),
            'severity' => $severity,
            'before' => $before,
            'after' => $after,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent() ? mb_substr($request->userAgent(), 0, 500) : null,
        ]);
    }
}
