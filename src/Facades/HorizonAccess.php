<?php

namespace Jauntin\HorizonAccess\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void defineGate()
 * @method static bool enabled()
 *
 * @see \Jauntin\HorizonAccess\HorizonAccess
 */
class HorizonAccess extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'HorizonAccess';
    }
}
