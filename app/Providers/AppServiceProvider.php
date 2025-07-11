<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    // ngrok testing local
    // public function boot(): void
    // {
    //     // ✅ Force HTTPS and proper root URL when running locally via ngrok
    //     if (app()->environment('local')) {
    //         URL::forceRootUrl(config('app.url'));
    //         URL::forceScheme('https');
    //     }
    // }

    // Production
    public function boot(): void
    {
        // Force HTTPS in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Log and customize the verification email
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            Log::info('🔗 Generated verification URL:', ['url' => $url]);

            return (new MailMessage)
                ->subject('Verify Your Email')
                ->line('Click below to verify your email.')
                ->action('Verify Email', html_entity_decode($url))
                ->line('If you did not create an account, no further action is required.');
        });
    }
}
