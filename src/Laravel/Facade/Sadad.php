<?php

namespace Sadad\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static request($callbackURL, $Amount, $Description, $Email = null, $Mobile = null, $additionalData = null)
 * @method static verify($amount, $authority)
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
