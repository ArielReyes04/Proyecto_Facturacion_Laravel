<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Permitir acceso - Las autorizaciones se manejan en middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|regex:/^\d+([,.]?\d{1,2})?$/|min:0.01|max:99999999.99',
            'stock' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'name.unique' => 'Ya existe un producto con este nombre.',
            'description.string' => 'La descripción debe ser texto.',
            'description.max' => 'La descripción no puede exceder 1000 caracteres.',
            'price.required' => 'El precio es obligatorio.',
            'price.regex' => 'El precio debe ser un número válido con máximo 2 decimales (puedes usar coma o punto).',
            'price.min' => 'El precio debe ser mayor que 0.',
            'price.max' => 'El precio no puede exceder 99,999,999.99.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
        ];
    }
}
