<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_name',
        'logo_path',
        'theme_preference',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'restaurant_name' => 'Javohir Restoran CRM',
            'theme_preference' => 'light',
        ]);
    }
}
