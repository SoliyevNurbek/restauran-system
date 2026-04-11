<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'hero_badge',
        'hero_title',
        'hero_text',
        'hero_primary_cta',
        'hero_secondary_cta',
        'hero_microcopy',
        'final_title',
        'final_text',
        'contact_title',
        'contact_text',
    ];
}
