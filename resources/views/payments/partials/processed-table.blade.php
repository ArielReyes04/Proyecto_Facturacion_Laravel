<div class="overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Factura</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Pago</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nº Transacción</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observaciones</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validado Por</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Validación</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @foreach ($payments as $payment)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">{{ $payment->invoice_id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $payment->payer ? $payment->payer->name : 'Desconocido' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($payment->amount, 2) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($payment->payment_type) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $payment->transaction_number }}</td>
            <td class="px-6 py-4">{{ $payment->observations ?? '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                @if($payment->status === 'aprobado')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aprobado</span>
                @elseif($payment->status === 'rechazado')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rechazado</span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $payment->validatedBy?->name ?? '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ optional($payment->validated_at)->format('d/m/Y H:i') ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
