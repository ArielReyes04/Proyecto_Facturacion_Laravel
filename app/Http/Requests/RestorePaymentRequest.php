<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => 'required|current_password',
            'reason' => 'required|string|min:5|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Debe ingresar su contraseña para confirmar.',
            'password.current_password' => 'La contraseña ingresada no es correcta.',
            'reason.required' => 'Debe ingresar una razón para restaurar el pago.',
            'reason.string' => 'La razón debe ser texto.',
            'reason.min' => 'La razón debe tener al menos 5 caracteres.',
            'reason.max' => 'La razón no debe exceder 255 caracteres.',
        ];
    }
}