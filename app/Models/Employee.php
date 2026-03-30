<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
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
