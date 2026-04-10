<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormacionAcademicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tipo_formacion' => $this->normalizeText($this->input('tipo_formacion', $this->input('type'))),
            'institucion' => $this->normalizeText($this->input('institucion', $this->input('institution'))),
            'nombre_carrera' => $this->normalizeText($this->input('nombre_carrera', $this->input('careerName'))),
            'fecha_inicio' => $this->input('fecha_inicio', $this->input('startDate')),
            'fecha_fin' => $this->input('fecha_fin', $this->input('endDate')),
        ]);
    }

    public function rules(): array
    {
        return [
            'tipo_formacion' => ['required', 'string', 'min:2', 'max:100'],
            'institucion' => ['required', 'string', 'min:2', 'max:255'],
            'nombre_carrera' => ['required', 'string', 'min:2', 'max:255'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_formacion.required' => 'El tipo de formacion es obligatorio.',
            'institucion.required' => 'La institucion es obligatoria.',
            'nombre_carrera.required' => 'La carrera es obligatoria.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
        ];
    }

    private function normalizeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return preg_replace('/\s+/', ' ', trim($value));
    }
}
