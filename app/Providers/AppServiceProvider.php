<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        try {
            if (Schema::hasTable('settings')) {
                View::share('appSetting', Setting::current());
            }
        } catch (\Throwable) {
            // Skip setting share during initial migration/install phases.
        }
    }
}
