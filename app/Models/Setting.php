<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_name',
        'contact_phone',
        'notification_email',
        'logo_path',
        'favicon_path',
        'theme_preference',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'restaurant_name' => 'Javohir Restoran CRM',
            'contact_phone' => null,
            'notification_email' => null,
            'theme_preference' => 'light',
        ]);
    }
}
