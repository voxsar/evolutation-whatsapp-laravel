<?php

namespace Voxsar\WhatsAppNotification;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Voxsar\WhatsAppNotification\Channels\WaapiChannel;

class WhatsAppNotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/whatsapp-notification.php',
            'whatsapp-notification'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/whatsapp-notification.php' => config_path('whatsapp-notification.php'),
            ], 'whatsapp-notification-config');
        }

        Notification::extend('waapi', function ($app) {
            return $app->make(WaapiChannel::class);
        });
    }
}