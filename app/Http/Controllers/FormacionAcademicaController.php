<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormacionAcademicaRequest;
use App\Models\FormacionAcademica;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormacionAcademicaController extends Controller
{
    public function store(FormacionAcademicaRequest $request): JsonResponse
    {
        try {
            $formacion = FormacionAcademica::create([
                ...$request->validated(),
                'usuario_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Formacion academica guardada correctamente',
                'formacion' => $formacion,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $formaciones = FormacionAcademica::forUser($request->user()->id)
                ->orderByDesc('fecha_inicio')
                ->orderByDesc('id')
                ->get();

            return response()->json([
                'formaciones' => $formaciones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
