<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OAuthUserService;
use GuzzleHttp\Client;
use Laravel\Socialite\Facades\Socialite;

class GitHubController extends Controller
{
    public function __construct(private readonly OAuthUserService $oAuthUserService) {}

    public function redirect()
    {
        return Socialite::driver('github')
            ->setHttpClient(new Client(['verify' => ! app()->isLocal()]))
            ->stateless()
            ->scopes(['read:user', 'user:email'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $githubUser = Socialite::driver('github')
                ->setHttpClient(new Client(['verify' => ! app()->isLocal()]))
                ->stateless()
                ->user();

            $user = $this->oAuthUserService->resolveOrCreateUser(
                'github',
                (string) $githubUser->getId(),
                $githubUser->getEmail(),
                $githubUser->getName() ?? $githubUser->getNickname() ?? 'Usuario',
            );

            $token = $this->oAuthUserService->issueToken($user);

            return response()
                ->view('github-callback', [
                    'token' => $token,
                    'user' => $user,
                ])
                ->header('Cross-Origin-Opener-Policy', 'same-origin-allow-popups')
                ->header('Cross-Origin-Embedder-Policy', 'unsafe-none');
        } catch (\Exception $e) {
            return response()
                ->view('github-callback', ['error' => $e->getMessage()])
                ->header('Cross-Origin-Opener-Policy', 'same-origin-allow-popups')
                ->header('Cross-Origin-Embedder-Policy', 'unsafe-none');
        }
    }
}
