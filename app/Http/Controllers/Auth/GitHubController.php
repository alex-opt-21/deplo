<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Usuario;
use Illuminate\Support\Str;

class GitHubController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            $user = Usuario::where('email', $githubUser->getEmail())->first();

            if (!$user) {
                $user = Usuario::create([
                    'email'       => $githubUser->getEmail(),
                    'nombre'      => $githubUser->getName() ?? $githubUser->getNickname() ?? 'Usuario',
                    'apellido'    => '',
                    'provider'    => 'github',
                    'provider_id' => $githubUser->getId(),
                    'rol'         => 'usuario',
                    'password'    => bcrypt(Str::random(24)),
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return view('github-callback', [
                'token' => $token,
                'user'  => $user,
            ]);

        } catch (\Exception $e) {
            return view('github-callback', ['error' => $e->getMessage()]);
        }
    }
}
