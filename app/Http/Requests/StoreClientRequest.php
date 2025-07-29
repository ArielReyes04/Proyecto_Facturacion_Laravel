<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|max:255|unique:clients,email',
            'phone' => 'nullable|string|max:10|regex:/^\d{1,10}$/',
            'address' => 'nullable|string|max:500',
            'document_type' => 'required|in:CE,RUC',
            'document_number' => [
                'required',
                'string',
                'max:20',
                'unique:clients,document_number',
                function ($attribute, $value, $fail) {
                    // Validar formato según tipo de documento
                    $documentType = request('document_type');
                    switch ($documentType) {
                        case 'CE':
                            if (!preg_match('/^\d{10}$/', $value)) {
                                $fail('La Cédula debe tener exactamente 10 dígitos.');
                            }
                            break;
                        case 'RUC':
                            if (!preg_match('/^\d{13}$/', $value)) {
                                $fail('El RUC debe tener exactamente 13 dígitos.');
                            }
                            break;
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
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe proporcionar un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede exceder 255 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'phone.regex' => 'El teléfono solo puede contener números (máximo 10 dígitos).',
            'phone.max' => 'El teléfono no puede exceder 10 dígitos.',
            'address.max' => 'La dirección no puede exceder 500 caracteres.',
            'document_type.required' => 'Debe seleccionar un tipo de documento.',
            'document_type.in' => 'El tipo de documento seleccionado no es válido.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.max' => 'El número de documento no puede exceder 20 caracteres.',
            'document_number.unique' => 'Este número de documento ya está registrado.',
        ];
    }
}
