<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DeleteUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasRole('Administrador');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'razon' => 'required|string|max:255',
            'password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('La contraseña es incorrecta.');
                    }
                }
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'razon.required' => 'La razón de eliminación es obligatoria.',
            'razon.string' => 'La razón debe ser un texto válido.',
            'razon.max' => 'La razón no puede exceder 255 caracteres.',
            'password.required' => 'La contraseña es obligatoria para confirmar la eliminación.',
            'password.string' => 'La contraseña debe ser un texto válido.',
        ];
    }

    /**
     * Additional validation after the basic rules.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = request()->route('user');
            if ($user && $user->id === Auth::id()) {
                $validator->errors()->add('user', 'No puedes eliminar tu propio usuario.');
            }
        });
    }
}
