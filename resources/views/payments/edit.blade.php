<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-yellow-500 leading-tight">
            Editar Pago
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('payments.update', $payment) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="invoice_id" class="block text-sm font-medium text-gray-700 mb-1">Factura</label>
                    <select name="invoice_id" id="invoice_id" required class="w-full border-gray-300 rounded-lg">
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" {{ old('invoice_id', $payment->invoice_id) == $invoice->id ? 'selected' : '' }}>
                                #{{ $invoice->id }} - {{ $invoice->client->name ?? 'Sin cliente' }}
                            </option>
                        @endforeach
                    </select>
                    @error('invoice_id')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pago</label>
                    <select name="payment_type" id="payment_type" required class="w-full border-gray-300 rounded-lg">
                        <option value="efectivo" {{ old('payment_type', $payment->payment_type) == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="tarjeta" {{ old('payment_type', $payment->payment_type) == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                        <option value="transferencia" {{ old('payment_type', $payment->payment_type) == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        <option value="cheque" {{ old('payment_type', $payment->payment_type) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                    @error('payment_type')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Monto</label>
                    <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount', $payment->amount) }}" required class="w-full border-gray-300 rounded-lg">
                    @error('amount')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="transaction_number" class="block text-sm font-medium text-gray-700 mb-1">N° de Transacción</label>
                    <input type="text" name="transaction_number" id="transaction_number" value="{{ old('transaction_number', $payment->transaction_number) }}" class="w-full border-gray-300 rounded-lg">
                    @error('transaction_number')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="observations" id="observations" class="w-full border-gray-300 rounded-lg">{{ old('observations', $payment->observations) }}</textarea>
                    @error('observations')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('payments.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancelar</a>
                    <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>