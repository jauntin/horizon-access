<?php

namespace Jauntin\HorizonAccess\Http\Controllers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\InvalidStateException;

class GithubController extends Controller
{
    /**
     * @param SocialiteManager $socialiteManager
     */
    public function __construct(
        private readonly SocialiteManager $socialiteManager
    ) {
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

    public function callback(): RedirectResponse|Redirector|Response|ResponseFactory
    {
        try {
            /** @var AbstractUser */
            $user = $this->getProvider()->user();
            Session::put(Config::get('horizon-access.session-key'), $user);
            Session::save();
            return redirect(Config::get('horizon-access.home'));
        } catch (ClientException | InvalidStateException $e) {
            Log::info($e->getMessage());
            return response('Unable to authenticate with Github. Please try again.', 500);
        }
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
