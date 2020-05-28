<?php

namespace Sadad\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static request($callbackURL, $amount, $dateTime, $orderId, $additionalData = null, $multiplexingData = null)
 * @method static verify($token)
 * @method static redirect()
 * @method static redirectUrl()
 * @method static getDriver()
 */
class Sadad extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Sadad';
    }
}
