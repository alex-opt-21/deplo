<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LinkedInController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('linkedin-openid')->redirect();
    }

    public function callback()
    {
        try {
            $linkedinUser = Socialite::driver('linkedin-openid')->user();

            $user = Usuario::where('email', $linkedinUser->getEmail())->first();

            if (!$user) {
                $user = Usuario::create([
                    'email'       => $linkedinUser->getEmail(),
                    'nombre'  => $linkedinUser->user['given_name'] ?? $linkedinUser->getName() ?? 'Usuario',
                    'apellido' => $linkedinUser->user['family_name'] ?? '',
                    'provider'    => 'linkedin',
                    'provider_id' => $linkedinUser->getId(),
                    'rol'         => 'usuario',
                    'password'    => bcrypt(Str::random(24)),
                ]);
            }

            // Si ya existe, no toca nada — solo loguea con los datos actuales de la BD
            $token = $user->createToken('auth_token')->plainTextToken;

            // Cierra el popup y manda los datos al frontend
            return view('linkedin-callback', [
                'token' => $token,
                'user'  => $user,
            ]);

        } catch (\Exception $e) {
            return view('linkedin-callback', ['error' => $e->getMessage()]);
        }
    }
}
