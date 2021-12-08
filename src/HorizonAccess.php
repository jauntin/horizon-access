<?php

namespace Jauntin\HorizonAccess;

use Illuminate\Support\Facades\Config;

class HorizonAccess
{
    public function defineGate(): void
    {
        resolve(GateProvider::class)->defineGate();
    }

    public function enabled(): bool
    {
        return Config::get('horizon-access.enabled');
    }
}
