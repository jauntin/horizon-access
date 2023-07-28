<?php

namespace Jauntin\HorizonAccess;

use Illuminate\Support\Facades\Config;

class HorizonAccess
{
    /**
     * @param GateProvider $gateProvider
     */
    public function __construct(
        private readonly GateProvider $gateProvider
    ) {
    }

    public function defineGate(): void
    {
        $this->gateProvider->defineGate();
    }

    public function enabled(): bool
    {
        return Config::get('horizon-access.enabled');
    }
}
