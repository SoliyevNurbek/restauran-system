<?php

namespace App\Models\Concerns;

use App\Models\VenueConnection;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

trait BelongsToTenant
{
    public function venueConnection(): BelongsTo
    {
        return $this->belongsTo(VenueConnection::class);
    }

    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            if (! TenantContext::scoped()) {
                return;
            }

            $tenantId = TenantContext::id();
            $model = $builder->getModel();

            if (! Schema::hasColumn($model->getTable(), 'venue_connection_id')) {
                return;
            }

            if ($tenantId) {
                $builder->where($model->qualifyColumn('venue_connection_id'), $tenantId);

                return;
            }

            $builder->whereRaw('1 = 0');
        });

        static::creating(function ($model): void {
            if (! TenantContext::scoped()) {
                return;
            }

            if (blank($model->venue_connection_id) && TenantContext::id()) {
                $model->venue_connection_id = TenantContext::id();
            }
        });
    }
}
