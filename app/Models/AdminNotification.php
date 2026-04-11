<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'icon',
        'status',
        'action_url',
        'related_type',
        'related_id',
        'is_read',
        'occurred_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'occurred_at' => 'datetime',
            'meta' => 'array',
        ];
    }
}
