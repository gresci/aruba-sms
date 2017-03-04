<?php

namespace NotificationChannels\SmscAruba;

use Illuminate\Support\ServiceProvider;

class SmscArubaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SmscArubaApi::class, function () {
            $config = config('services.smsaruba');

            return new SmscArubaApi($config['login'], $config['secret'], $config['sender']);
        });
    }
}
