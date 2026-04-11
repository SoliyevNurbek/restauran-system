<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCategory extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'venue_connection_id',
        'name',
        'type',
    ];
}
