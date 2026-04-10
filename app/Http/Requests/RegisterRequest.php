<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => $this->normalizeText($this->input('nombre')),
            'apellido' => $this->normalizeText($this->input('apellido')),
            'email' => is_string($this->input('email')) ? trim(mb_strtolower($this->input('email'))) : $this->input('email'),
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'min:2', 'max:255'],
            'apellido' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Debes enviar un correo valido.',
            'email.unique' => 'Ya existe una cuenta registrada con este correo.',
            'password.required' => 'La contrasena es obligatoria.',
            'password.confirmed' => 'La confirmacion de la contrasena no coincide.',
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
