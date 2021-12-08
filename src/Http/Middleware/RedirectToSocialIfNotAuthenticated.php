<?php

namespace Jauntin\HorizonAccess\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Jauntin\HorizonAccess\Facades\HorizonAccess;

class RedirectToSocialIfNotAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (
            HorizonAccess::enabled() &&
            !Session::get(Config::get('horizon-access.session-key'))
        ) {
            return redirect(Config::get('horizon-access.redirect'));
        }
        return $next($request);
    }
}
