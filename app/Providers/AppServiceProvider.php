<?php

namespace App\Providers;

use App\Models\MediaAsset;
use App\Models\LanguageLine;
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

        View::composer('*', function ($view): void {
            try {
                $request = request();
                $sharedUser = $request->user();
                $routeName = (string) optional($request->route())->getName();
                $usesGlobalBranding = $this->usesGlobalBranding($routeName);

                $view->with('appSetting', Schema::hasTable('settings')
                    ? (($sharedUser && ! $usesGlobalBranding && ! $sharedUser->isSuperAdmin())
                        ? Setting::currentFor($sharedUser)
                        : Setting::global())
                    : null);

                $view->with('mediaAssets', MediaAsset::keyed(($usesGlobalBranding || $sharedUser?->isSuperAdmin()) ? null : $sharedUser));
                $view->with('languageLines', LanguageLine::allGrouped());
            } catch (\Throwable) {
                // Skip setting share during initial migration/install phases.
                $view->with('appSetting', null);
                $view->with('mediaAssets', collect());
                $view->with('languageLines', collect());
            }
        });
    }

    private function usesGlobalBranding(string $routeName): bool
    {
        if ($routeName === '') {
            return true;
        }

        if (str_starts_with($routeName, 'superadmin.')) {
            return true;
        }

        return in_array($routeName, [
            'landing',
            'login',
            'login.store',
            'register',
            'register.store',
        ], true);
    }
}
