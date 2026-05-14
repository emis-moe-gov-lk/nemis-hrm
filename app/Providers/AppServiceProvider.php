<?php

namespace App\Providers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\ServiceProvider;
use App\Observers\ActivityObserver;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Activity::observe(ActivityObserver::class);

        $forceHttps = filter_var(
            env('FORCE_HTTPS', app()->environment('production')),
            FILTER_VALIDATE_BOOL
        );

        if ($forceHttps) {
            URL::forceScheme('https');
        }
    }
}
