<?php

namespace Jauntin\HorizonAccess;

use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Jauntin\HorizonAccess\Http\Middleware\RedirectToSocialIfNotAuthenticated;

class HorizonAccessServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('horizon-access.php'),
            ], 'config');
        }
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        /** @var Router */
        $router = $this->app->make(Router::class);
        $router->middlewareGroup('social-auth', [
            StartSession::class,
        ]);
        $router->middlewareGroup(Config::get('horizon-access.middleware'), [
            StartSession::class,
            RedirectToSocialIfNotAuthenticated::class,
        ]);
    }

    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'horizon-access');
        $this->app->bind('HorizonAccess', function ($app) {
            return new HorizonAccess();
        });
    }
}
