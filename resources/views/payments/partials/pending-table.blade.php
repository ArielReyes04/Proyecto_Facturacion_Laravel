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
                <div class="flex space-x-2">
                    <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:underline" title="Ver"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('payments.edit', $payment) }}" class="text-yellow-600 hover:underline" title="Editar"><i class="fas fa-edit"></i></a>
                    <!-- Botón para abrir el modal -->
                    <button type="button"
                        onclick="openDeleteModal({{ $payment->id }}, '{{ $payment->amount }}', '{{ ucfirst($payment->payment_type) }}')"
                        class="text-red-600 hover:underline" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

<!-- Modal de eliminación global (fuera del foreach) -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
    <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-2xl space-y-4">
        <h3 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
            Eliminar Pago
        </h3>
        <p class="text-sm text-gray-600">
            Está a punto de eliminar el pago de <strong id="deletePaymentType"></strong> por $<span id="deletePaymentAmount"></span>
        </p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="mb-3">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Razón de eliminación</label>
                <textarea
                        name="reason"
                        class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                        rows="3"
                        placeholder="Ingrese el motivo de la eliminación..."
                        required></textarea>
            </div>
            <div class="mb-3">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password" id="password" required class="w-full border rounded px-3 py-2" placeholder="Ingrese su contraseña">
            </div>
            <div class="flex justify-end gap-2">
                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Eliminar Pago
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function openDeleteModal(paymentId, paymentAmount, paymentType) {
        document.getElementById('deletePaymentType').textContent = paymentType;
        document.getElementById('deletePaymentAmount').textContent = paymentAmount;
        document.getElementById('deleteForm').action = `/payments/${paymentId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.getElementById('deleteForm').reset();
    }
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>
