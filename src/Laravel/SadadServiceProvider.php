<?php

namespace Sadad\Laravel;

use Illuminate\Support\ServiceProvider;
use Sadad\Drivers\DriverInterface;
use Sadad\Drivers\RestDriver;
use Sadad\Sadad;

class SadadServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DriverInterface::class, function () {
            return new RestDriver();
        });

        $this->app->singleton('Sadad', function () {
            $merchantID = config('services.sadad.merchantID', config('Sadad.merchantID', '00000000000000'));
            $terminalId = config('services.sadad.terminalId', config('Sadad.terminalId', '00000000'));
            $key = config('services.sadad.key', config('Sadad.key', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'));

            $sadad = new Sadad($merchantID, $terminalId, $key, $this->app->make(DriverInterface::class));

            return $sadad;
        });
    }

    /**
     * Publish the plugin configuration.
     */
    public function boot()
    {
        //
    }
}
