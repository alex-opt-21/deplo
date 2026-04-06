<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function completar(Request $request)
    {
        try {
            $usuario = $request->user();
$datos = [];

            if ($request->filled('biografia'))
                $datos['biografia'] = $request->biografia;

            if ($request->filled('ubicacion'))
                $datos['ubicacion'] = $request->ubicacion;

            // foto perfil


            if ($request->hasFile('foto_perfil')) {
                $path = $request->file('foto_perfil')->store('fotos_perfil', 'public');
                $datos['foto_perfil'] = $path;
            }

            $datos['perfil_completado'] = 1;


            Profile::updateOrCreate(
                ['usuario_id' => $usuario->id],
                $datos
            );

            // guarda en usuarios
            $usuario->update($datos);


            return response()->json([
                'message' => 'Perfil completado correctamente',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function crearPerfilProfesional(Request $request)
    {
        try {
            $usuario = $request->user();

            $profile = Profile::where('usuario_id', $usuario->id)->first();

            if (!$profile) {
                return response()->json([
                    'message' => 'Primero debe completar su perfil básico'
                ], 400);
            }

            $datos = [];

            if ($request->filled('titulo'))
                $datos['titulo'] = $request->titulo;

            if ($request->filled('skills'))
                $datos['skills'] = $request->skills;

            if ($request->filled('github'))
                $datos['github'] = $request->github;

            if ($request->filled('linkedin'))
                $datos['linkedin'] = $request->linkedin;

            $profile->update($datos);

            return response()->json([
                'message' => 'Perfil profesional actualizado correctamente',
                'profile' => $profile
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔍 BÚSQUEDA DE USUARIOS
    public function searchUsers(Request $request)
    {
        try {
            $query = trim($request->query('q', ''));

            if ($query === '') {
                return response()->json([]);
            }

            // 🔹 Normalizar espacios múltiples
            $words = preg_split('/\s+/', $query);

            $usersQuery = Usuario::query();

            // 🔹 CASO 1: una sola palabra
            if (count($words) === 1) {
                $term = $words[0];

                $usersQuery->where(function ($q) use ($term) {
                    $q->where('nombre', 'LIKE', "%{$term}%")
                      ->orWhere('apellido', 'LIKE', "%{$term}%")
                      ->orWhereHas('habilidades', function ($h) use ($term) {
                          $h->where('nombre', 'LIKE', "%{$term}%");
                      });
                });
            }

            // 🔹 CASO 2: nombre + apellido
            elseif (count($words) === 2) {
                [$nombre, $apellido] = $words;

                $usersQuery->where('nombre', 'LIKE', "%{$nombre}%")
                           ->where('apellido', 'LIKE', "%{$apellido}%");
            }

            // 🔹 CASO 3: nombre + múltiples apellidos
            else {
                $nombre = array_shift($words);
                $apellido = implode(' ', $words);

                $usersQuery->where('nombre', 'LIKE', "%{$nombre}%")
                           ->where('apellido', 'LIKE', "%{$apellido}%");
            }

            // 🔹 Traer habilidades
            $users = $usersQuery
                ->with('habilidades')
                ->limit(20)
                ->get();

            // 🔹 Formato para frontend
            $result = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->nombre,
                    'lastName' => $user->apellido,
                    'photo' => $user->foto_perfil,
                    'bio' => $user->biografia,
                    'skills' => $user->habilidades->pluck('nombre')->values(),
                ];
            });

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error en la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
