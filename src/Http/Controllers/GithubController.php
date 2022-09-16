<?php

namespace Jauntin\HorizonAccess\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Two\GithubProvider;

class GithubController extends Controller
{
    private SocialiteManager $socialiteManager;

    /**
     * @param SocialiteManager $socialiteManager
     */
    public function __construct(SocialiteManager $socialiteManager)
    {
        $this->socialiteManager = $socialiteManager;

        $this->middleware('social-auth');
    }

    /**
     * @return RedirectResponse
     */
    public function redirect(): RedirectResponse
    {
        return $this->getProvider()
            ->scopes(['read:org'])
            ->redirect();
    }

    /**
     * @return RedirectResponse
     */
    public function callback(): RedirectResponse
    {
        /** @var AbstractUser */
        $user = $this->getProvider()->user();
        Session::put(Config::get('horizon-access.session-key'), $user);
        Session::save();

        return redirect(Config::get('horizon-access.home'));
    }

    /**
     * @return GithubProvider
     */
    private function getProvider(): GithubProvider
    {
        /** @var GithubProvider */
        return $this->socialiteManager->buildProvider(GithubProvider::class, Config::get('horizon-access.github'));
    }
}
