<div class="overflow-x-auto w-full">
    <table class="w-full table-fixed bg-white rounded-lg shadow-md overflow-hidden">
        <thead class="bg-gradient-to-r from-yellow-100 to-yellow-200">
            <tr>
                <th class="w-20 px-2 py-3 text-center text-xs font-bold text-yellow-700 uppercase tracking-wider">ID Factura</th>
                <th class="w-40 px-2 py-3 text-center text-xs font-bold text-yellow-700 uppercase tracking-wider">Cliente</th>
                <th class="w-24 px-2 py-3 text-center text-xs font-bold text-yellow-700 uppercase tracking-wider">Monto</th>
                <th class="w-32 px-2 py-3 text-center text-xs font-bold text-yellow-700 uppercase tracking-wider">Tipo de Pago</th>
                <th class="w-36 px-2 py-3 text-center text-xs font-bold text-yellow-700 uppercase tracking-wider">Nº Transacción</th>
                <th class="w-48 px-2 py-3 text-center text-xs font-bold text-yellow-700 uppercase tracking-wider">Observaciones</th>
                <th class="w-24 px-2 py-3 text-center text-xs font-bold text-yellow-700 uppercase tracking-wider">Estado</th>
                <th class="w-40 px-2 py-3 text-center text-xs font-bold text-yellow-700 uppercase tracking-wider">Validado por</th>
                <th class="w-32 px-2 py-3 text-center text-xs font-bold text-yellow-700 uppercase tracking-wider">Validado el</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
            <tr class="hover:bg-yellow-50 transition-colors">
                <td class="w-20 px-2 py-2 text-center font-semibold text-gray-700">{{ $payment->invoice_id }}</td>
                <td class="w-40 px-2 py-2 text-center break-words">{{ $payment->payer ? $payment->payer->name : 'Desconocido' }}</td>
                <td class="w-24 px-2 py-2 text-center text-green-700 font-bold">${{ number_format($payment->amount, 2) }}</td>
                <td class="w-32 px-2 py-2 text-center">{{ ucfirst($payment->payment_type) }}</td>
                <td class="w-36 px-2 py-2 text-center">{{ $payment->transaction_number }}</td>
                <td class="w-48 px-2 py-2 text-center break-words text-gray-500">{{ $payment->observations ?? '-' }}</td>
                <td class="w-24 px-2 py-2 text-center">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $payment->status === 'aprobado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
                <td class="w-40 px-2 py-2 text-center break-words">{{ $payment->validator ? $payment->validator->name : '-' }}</td>
                <td class="w-32 px-2 py-2 text-center">{{ $payment->validated_at ? $payment->validated_at->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-6 text-center text-gray-400">No hay pagos procesados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
