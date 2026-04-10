<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class OAuthUserService
{
    public function resolveOrCreateUser(
        string $provider,
        string $providerId,
        ?string $email,
        string $nombre,
        string $apellido = ''
    ): Usuario {
        $normalizedEmail = $email ? trim(mb_strtolower($email)) : null;

        $user = $normalizedEmail
            ? Usuario::where('email', $normalizedEmail)->first()
            : null;

        $user ??= Usuario::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if ($user) {
            $updates = [];

            if (! $user->provider) {
                $updates['provider'] = $provider;
            }

            if (! $user->provider_id) {
                $updates['provider_id'] = $providerId;
            }

            if ($normalizedEmail && ! $user->email) {
                $updates['email'] = $normalizedEmail;
            }

            if (blank($user->nombre) && $nombre !== '') {
                $updates['nombre'] = $nombre;
            }

            if (blank($user->apellido) && $apellido !== '') {
                $updates['apellido'] = $apellido;
            }

            if ($updates !== []) {
                $user->update($updates);
                $user->refresh();
            }

            return $user;
        }

        if (! $normalizedEmail) {
            throw new RuntimeException('No fue posible recuperar el correo del proveedor.');
        }

        return Usuario::create([
            'email' => $normalizedEmail,
            'nombre' => $nombre !== '' ? $nombre : 'Usuario',
            'apellido' => $apellido,
            'provider' => $provider,
            'provider_id' => $providerId,
            'rol' => 'usuario',
            'password' => Hash::make(Str::random(24)),
        ]);
    }

    public function issueToken(Usuario $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }
}
