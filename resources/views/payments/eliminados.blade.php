<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-red-600 leading-tight">
                {{ __('Pagos Eliminados') }}
            </h2>
            <a href="{{ route('payments.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            @if($deletedPayments->isEmpty())
                <p class="text-gray-500">No hay pagos eliminados.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Factura</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Eliminado el</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deletedPayments as $payment)
                                <tr>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">{{ $payment->id }}</td>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">#{{ $payment->invoice_id }}</td>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">${{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">{{ $payment->payment_type }}</td>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">{{ $payment->deleted_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button type="button"
                                                onclick="openRestoreModal({{ $payment->id }})"
                                                class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700" title="Restaurar">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button type="button"
                                                onclick="openForceDeleteModal({{ $payment->id }})"
                                                class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700" title="Eliminar definitivamente">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Restaurar -->
    <div id="restoreModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-2xl space-y-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                Motivo para restaurar pago
            </h3>
            <form id="restoreForm" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="restore_reason" class="block text-sm font-medium text-gray-700 mb-1">Razón</label>
                    <textarea
                        name="reason"
                        class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                        rows="3"
                        placeholder="Ingrese el motivo de la eliminación..."
                        required></textarea>
                </div>
                <div class="mb-3">
                    <label for="restore_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="password" id="restore_password" required class="w-full border rounded px-3 py-2" placeholder="Ingrese su contraseña">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRestoreModal()" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Restaurar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Eliminar Definitivo -->
    <div id="forceDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-2xl space-y-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                Motivo para eliminar permanentemente
            </h3>
            <form id="forceDeleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="mb-3">
                    <label for="force_reason" class="block text-sm font-medium text-gray-700 mb-1">Razón</label>
                    <textarea
                        name="reason"
                        class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                        rows="3"
                        placeholder="Ingrese el motivo de la eliminación..."
                        required></textarea>
                </div>
                <div class="mb-3">
                    <label for="force_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="password" id="force_password" required class="w-full border rounded px-3 py-2" placeholder="Ingrese su contraseña">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeForceDeleteModal()" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRestoreModal(paymentId) {
            document.getElementById('restoreForm').action = `/payments/${paymentId}/restore`;
            document.getElementById('restoreModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeRestoreModal() {
            document.getElementById('restoreModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.getElementById('restoreForm').reset();
        }
        function openForceDeleteModal(paymentId) {
            document.getElementById('forceDeleteForm').action = `/payments/${paymentId}/force-delete`;
            document.getElementById('forceDeleteModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeForceDeleteModal() {
            document.getElementById('forceDeleteModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.getElementById('forceDeleteForm').reset();
        }
        document.getElementById('restoreModal').addEventListener('click', function(e) {
            if (e.target === this) closeRestoreModal();
        });
        document.getElementById('forceDeleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeForceDeleteModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRestoreModal();
                closeForceDeleteModal();
            }
        });
    </script>
</x-app-layout>