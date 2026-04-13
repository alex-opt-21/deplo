<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OAuthUserService;
use GuzzleHttp\Client;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function __construct(private readonly OAuthUserService $oAuthUserService) {}

    public function redirect()
    {
        return Socialite::driver('google')
            ->setHttpClient(new Client(['verify' => ! app()->isLocal()]))
            ->stateless()
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->setHttpClient(new Client(['verify' => ! app()->isLocal()]))
                ->stateless()
                ->user();

            $user = $this->oAuthUserService->resolveOrCreateUser(
                'google',
                (string) $googleUser->getId(),
                $googleUser->getEmail(),
                $googleUser->user['given_name'] ?? $googleUser->getName() ?? 'Usuario',
                $googleUser->user['family_name'] ?? '',
            );

            $token = $this->oAuthUserService->issueToken($user);

            return response()
                ->view('google-callback', [
                    'token' => $token,
                    'user' => $user,
                ])
                ->header('Cross-Origin-Opener-Policy', 'same-origin-allow-popups')
                ->header('Cross-Origin-Embedder-Policy', 'unsafe-none');
        } catch (\Exception $e) {
            return response()
                ->view('google-callback', ['error' => $e->getMessage()])
                ->header('Cross-Origin-Opener-Policy', 'same-origin-allow-popups')
                ->header('Cross-Origin-Embedder-Policy', 'unsafe-none');
        }
    }
}
