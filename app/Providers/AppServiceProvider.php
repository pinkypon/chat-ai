<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    // public function boot(): void
    // {
    //     if (app()->environment('local')) {
    //         URL::forceRootUrl(config('app.url'));
    //         URL::forceScheme('https');
    //     }
    // }
}
