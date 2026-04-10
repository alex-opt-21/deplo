<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OAuthUserService;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function __construct(private readonly OAuthUserService $oAuthUserService) {}

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = $this->oAuthUserService->resolveOrCreateUser(
                'google',
                (string) $googleUser->getId(),
                $googleUser->getEmail(),
                $googleUser->user['given_name'] ?? $googleUser->getName() ?? 'Usuario',
                $googleUser->user['family_name'] ?? '',
            );

            $token = $this->oAuthUserService->issueToken($user);

            return view('google-callback', [
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return view('google-callback', ['error' => $e->getMessage()]);
        }
    }
}
