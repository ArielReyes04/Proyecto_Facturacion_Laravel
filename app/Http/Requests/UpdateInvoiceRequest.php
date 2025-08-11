<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        // Puedes personalizar la autorización según tus necesidades
        return true;
    }

    public function rules()
    {
        return [
            'client_id' => 'sometimes|exists:clients,id',
            'products' => 'sometimes|array',
            'products.*.id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|integer|min:1',
            'status' => 'sometimes|in:pendiente,active,cancelado',
            // Agrega aquí otros campos que se puedan actualizar
        ];
    }

    public function messages()
    {
        return [
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'products.*.id.exists' => 'Uno de los productos seleccionados no existe.',
            'products.*.quantity.min' => 'La cantidad debe ser al menos 1.',
        ];
    }
}