<?php

namespace Tests\Http\Controllers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Jauntin\HorizonAccess\Http\Controllers\GithubController;
use Jauntin\HorizonAccess\Tests\TestCase;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Two\User as SocialiteUser;
use PHPUnit\Framework\Attributes\DataProvider;

class GithubControllerTest extends TestCase
{
    use WithoutMiddleware;

    public function testRedirect(): void
    {
        $provider = $this->createMock(GithubProvider::class);
        $provider->method('scopes')->willReturnSelf();
        $provider->method('redirect')->willReturn(redirect('/mocked_redirect'));

        $manager = $this->createMock(SocialiteManager::class);
        $manager->method('buildProvider')->willReturn($provider);

        $controller = new GithubController($manager);
        $response = $controller->redirect();

        $this->assertEquals(Config::get('app.url') . '/mocked_redirect', $response->getTargetUrl());
    }

    public function testCallbackSuccess(): void
    {
        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 123;

        $provider = $this->createMock(GithubProvider::class);
        $provider->method('user')->willReturn($socialiteUser);

        $manager = $this->createMock(SocialiteManager::class);
        $manager->method('buildProvider')->willReturn($provider);

        Config::set('horizon-access.session-key', 'session-key');
        Config::set('horizon-access.github', 'github');
        Config::set('horizon-access.home', 'home');

        $controller = new GithubController($manager);
        $response = $controller->callback();

        $this->assertEquals(Config::get('app.url') . '/home', $response->getTargetUrl());
        $this->assertEquals($socialiteUser, Session::get('session-key'));
    }

    #[DataProvider(methodName: 'callbackErrorDataProvider')]
    public function testCallbackError($exception): void
    {
        $provider = $this->createMock(GithubProvider::class);
        $provider->method('user')->willThrowException($exception);

        $manager = $this->createMock(SocialiteManager::class);
        $manager->method('buildProvider')->willReturn($provider);

        Log::shouldReceive('info')->with($exception->getMessage());

        $controller = new GithubController($manager);
        $response = $controller->callback();

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Unable to authenticate with Github. Please try again.', $response->getContent());
    }

    public static function callbackErrorDataProvider(): array
    {
        return [
            'throw InvalidStateException' => [
                new InvalidStateException('mocked exception'),
            ],
            'throw ClientException' => [
                new ClientException('mocked exception', new Request('GET', '/'), new Response()),
            ],
        ];
    }
}
