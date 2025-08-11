<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'payment_type' => 'required|in:efectivo,tarjeta,transferencia,cheque',
            'amount' => 'required|numeric|min:0.01',
            'transaction_number' => 'nullable|string|max:100',
            'observations' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_id.required' => 'La factura es obligatoria.',
            'invoice_id.exists' => 'La factura seleccionada no es válida.',
            'payment_type.required' => 'El tipo de pago es obligatorio.',
            'payment_type.in' => 'El tipo de pago seleccionado no es válido.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.numeric' => 'El monto debe ser un número.',
            'amount.min' => 'El monto debe ser mayor a 0.',
            'transaction_number.string' => 'El número de transacción debe ser texto.',
            'transaction_number.max' => 'El número de transacción no debe exceder 100 caracteres.',
            'observations.string' => 'Las observaciones deben ser texto.',
            'observations.max' => 'Las observaciones no deben exceder 255 caracteres.',
        ];
    }
}