<?php

namespace Jauntin\HorizonAccess;

use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Jauntin\HorizonAccess\Http\Middleware\RedirectToSocialIfNotAuthenticated;
use Laravel\Socialite\Two\User;
use Throwable;

class HorizonAccessServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
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

        $this->defineGate();
    }

    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'horizon-access');
    }

    private function defineGate(): void
    {
        Gate::define('viewHorizon', function ($user = null) {
            return $this->userShouldHaveAccess();
        });
    }

    private function userShouldHaveAccess(): bool
    {
        try {
            /** @var User */
            $user = Session::get(Config::get('horizon-access.session-key'));
            $teamMembers = $this->getTeamMembers($user->token);
            return collect($teamMembers)->map(fn ($a) => $a['login'])->search($user->user['login']) !== false;
        } catch (Throwable $e) {
            return false;
        }
    }

    private function getTeamMembers(string $token): array
    {
        $teamMembers = Cache::get('jauntin-github-team-members' . $token);
        if ($teamMembers === null) {
            $teamMembers = Http::withHeaders([
                'accept' => 'application/vnd.github.v3+json',
                'Authorization' => 'token ' . $token,
            ])->get('https://api.github.com' . Config::get('horizon-access.team-members-uri'))->json();
            Cache::put('jauntin-github-team-members' . $token, $teamMembers, 3600);
        }
        return $teamMembers;
    }
}
