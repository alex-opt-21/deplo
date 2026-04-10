<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OAuthUserService;
use Laravel\Socialite\Facades\Socialite;

class LinkedInController extends Controller
{
    public function __construct(private readonly OAuthUserService $oAuthUserService) {}

    public function redirect()
    {
        return Socialite::driver('linkedin-openid')->redirect();
    }

    public function callback()
    {
        try {
            $linkedinUser = Socialite::driver('linkedin-openid')->user();

            $user = $this->oAuthUserService->resolveOrCreateUser(
                'linkedin',
                (string) $linkedinUser->getId(),
                $linkedinUser->getEmail(),
                $linkedinUser->user['given_name'] ?? $linkedinUser->getName() ?? 'Usuario',
                $linkedinUser->user['family_name'] ?? '',
            );

            $token = $this->oAuthUserService->issueToken($user);

            return view('linkedin-callback', [
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return view('linkedin-callback', ['error' => $e->getMessage()]);
        }
    }
}
