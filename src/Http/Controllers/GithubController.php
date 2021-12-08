<?php

namespace Jauntin\HorizonAccess\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Two\GithubProvider;

class GithubController extends Controller
{
    public function __construct()
    {
        $this->middleware('social-auth');
    }
    public function redirect()
    {
        return $this->getProvider()
            ->scopes(['read:org'])
            ->redirect();
    }

    public function callback()
    {
        /** @var AbstractUser */
        $user = $this->getProvider()->user();
        Session::put(Config::get('horizon-access.session-key'), $user);
        Session::save();
        return redirect(Config::get('horizon-access.home'));
    }

    private function getProvider(): GithubProvider
    {
        return resolve(SocialiteManager::class)
            ->buildProvider(
                GithubProvider::class,
                Config::get('horizon-access.github')
            );
    }
}
