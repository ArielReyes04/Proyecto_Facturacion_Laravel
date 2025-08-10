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
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
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
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Pendiente
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <form action="{{ route('payments.approve', $payment->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Aprobar" class="text-green-600 hover:text-green-800">
                        <i class="fas fa-check-circle"></i>
                    </button>
                </form>
                <button type="button" title="Rechazar" class="text-red-600 hover:text-red-800" onclick="openRejectModal({{ $payment->id }})">
                    <i class="fas fa-times-circle"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
