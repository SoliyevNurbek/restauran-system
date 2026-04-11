<?php

namespace App\Services\SuperAdmin;

use App\Models\User;
use App\Models\VenueConnection;

class VenueAccessSyncService
{
    public function sync(VenueConnection $venue, string $status): ?User
    {
        $user = $venue->pendingUser()->first();

        if (! $user) {
            return null;
        }

        if ($status === 'approved') {
            $user->forceFill([
                'name' => $venue->owner_name,
                'username' => $venue->username,
                'status' => 'active',
                'role' => 'admin',
                'venue_connection_id' => $venue->getKey(),
            ])->save();

            if ($venue->admin_user_id !== $user->getKey()) {
                $venue->forceFill([
                    'admin_user_id' => $user->getKey(),
                ])->save();
            }

            return $user;
        }

        if (in_array($status, ['rejected', 'suspended'], true)) {
            $user->forceFill([
                'status' => 'suspended',
            ])->save();

            return $user;
        }

        $user->forceFill([
            'status' => 'pending',
        ])->save();

        return $user;
    }
}
