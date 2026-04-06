<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function storeOrUpdate(Request $request)
{
    try {
        $usuario = $request->user();

        // Todo va directo a tabla 'usuarios'
        $datosUsuario = [];

        if ($request->filled('nombre'))
            $datosUsuario['nombre'] = $request->nombre;
        if ($request->filled('apellido'))
            $datosUsuario['apellido'] = $request->apellido;
        if ($request->filled('biografia'))
            $datosUsuario['biografia'] = $request->biografia;
        if ($request->filled('ubicacion'))
            $datosUsuario['ubicacion'] = $request->ubicacion;
        if ($request->filled('fecha_nacimiento'))
            $datosUsuario['fecha_nacimiento'] = $request->fecha_nacimiento;

        if ($request->hasFile('foto_perfil')) {
            $datosUsuario['foto_perfil'] = $request->file('foto_perfil')
                ->store('fotos_perfil', 'public');
        }
        if ($request->hasFile('foto_portada')) {
            $datosUsuario['foto_portada'] = $request->file('foto_portada')
                ->store('fotos_portada', 'public');
        }

        if (!empty($datosUsuario)) {
            $usuario->update($datosUsuario);
        }

        // Redes sociales van a tabla 'social'
        foreach (['github', 'linkedin'] as $red) {
            if ($request->filled($red)) {
                \App\Models\Social::updateOrCreate(
                    [
                        'usuario_id'        => $usuario->id,
                        'nombre_plataforma' => $red,
                    ],
                    ['url_plataforma' => $request->$red]
                );
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Perfil actualizado correctamente',
            'data'    => $usuario->fresh(),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function show(Request $request)
{
    $usuario = $request->user();

    // Traer redes sociales
    $github   = \App\Models\Social::where('usuario_id', $usuario->id)
                    ->where('nombre_plataforma', 'github')
                    ->first();
    $linkedin = \App\Models\Social::where('usuario_id', $usuario->id)
                    ->where('nombre_plataforma', 'linkedin')
                    ->first();

    return response()->json([
        'nombre'             => $usuario->nombre          ?? '',
        'apellido'           => $usuario->apellido        ?? '',
        'email'              => $usuario->email           ?? '',
        'biografia'          => $usuario->biografia       ?? '',
        'ubicacion'          => $usuario->ubicacion       ?? '',
        'fecha_nacimiento'   => $usuario->fecha_nacimiento ?? '',
        'foto_perfil'        => $usuario->foto_perfil     ?? '',
        'foto_portada'       => $usuario->foto_portada    ?? '',
        'perfil_completado'  => $usuario->perfil_completado ?? 0,
        'github'             => $github?->url_plataforma  ?? '',
        'linkedin'           => $linkedin?->url_plataforma ?? '',
    ]);
}
    public function completar(Request $request)
    {
        try {
            $usuario = $request->user();

            $datos = $request->only(['biografia', 'ubicacion']);

            if ($request->hasFile('foto_perfil')) {
                $datos['foto_perfil'] = $request->file('foto_perfil')->store('fotos_perfil', 'public');
            }

            $datos['perfil_completado'] = true;
            $datos['estado'] = 'activo'; // Campo nuevo en tu BD

            // Guardar directamente en la tabla 'usuarios'
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

        // Verificar que el perfil básico esté completo
        if (!$usuario->perfil_completado) {
            return response()->json([
                'message' => 'Primero debe completar su perfil básico'
            ], 400);
        }

        // Actualizar campos de perfil en tabla 'usuarios'
        $datosUsuario = [];

        if ($request->filled('biografia'))
            $datosUsuario['biografia'] = $request->biografia;

        if ($request->filled('ubicacion'))
            $datosUsuario['ubicacion'] = $request->ubicacion;

        if ($request->filled('fecha_nacimiento'))
            $datosUsuario['fecha_nacimiento'] = $request->fecha_nacimiento;

        if ($request->hasFile('foto_perfil')) {
            $datosUsuario['foto_perfil'] = $request->file('foto_perfil')
                ->store('fotos_perfil', 'public');
        }

        if (!empty($datosUsuario)) {
            $usuario->update($datosUsuario);
        }

        // Guardar redes sociales en tabla 'social'
        $redes = [
            'github'   => $request->github,
            'linkedin' => $request->linkedin,
        ];

        foreach ($redes as $plataforma => $url) {
            if (!empty($url)) {
                \App\Models\Social::updateOrCreate(
                    [
                        'usuario_id'        => $usuario->id,
                        'nombre_plataforma' => $plataforma,
                    ],
                    ['url_plataforma' => $url]
                );
            }
        }

        return response()->json([
            'message' => 'Perfil profesional actualizado correctamente',
            'usuario' => $usuario->fresh(),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
