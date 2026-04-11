<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'venue_connection_id',
        'full_name',
        'role',
        'phone',
        'salary',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
        ];
    }
}
