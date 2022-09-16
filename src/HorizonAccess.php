<?php

namespace Jauntin\HorizonAccess;

use Illuminate\Support\Facades\Config;

class HorizonAccess
{
    private GateProvider $gateProvider;

    /**
     * @param GateProvider $gateProvider
     */
    public function __construct(GateProvider $gateProvider)
    {
        $this->gateProvider = $gateProvider;
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
