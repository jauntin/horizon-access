<?php

namespace Jauntin\HorizonAccess\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Jauntin\HorizonAccess\Facades\HorizonAccess;

class RedirectToSocialIfNotAuthenticated
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return Response|RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
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
