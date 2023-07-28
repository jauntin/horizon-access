<?php

namespace Jauntin\HorizonAccess;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Two\User;
use Throwable;

class GateProvider
{
    public function defineGate(): void
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

    /**
     * @return array<int, array<string, string>>
     */
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
