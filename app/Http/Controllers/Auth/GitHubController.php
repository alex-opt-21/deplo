<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OAuthUserService;
use Laravel\Socialite\Facades\Socialite;

class GitHubController extends Controller
{
    public function __construct(private readonly OAuthUserService $oAuthUserService) {}

    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            $user = $this->oAuthUserService->resolveOrCreateUser(
                'github',
                (string) $githubUser->getId(),
                $githubUser->getEmail(),
                $githubUser->getName() ?? $githubUser->getNickname() ?? 'Usuario',
            );

            $token = $this->oAuthUserService->issueToken($user);

            return view('github-callback', [
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return view('github-callback', ['error' => $e->getMessage()]);
        }
    }
}
