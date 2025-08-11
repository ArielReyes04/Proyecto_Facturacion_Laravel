<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-yellow-500 leading-tight">
                Detalle de Pago
            </h2>
            <a href="{{ route('payments.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <dl class="divide-y divide-gray-200">
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Factura</dt>
                    <dd>#{{ $payment->invoice_id }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Cliente</dt>
                    <dd>{{ $payment->payer ? $payment->payer->name : 'Desconocido' }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Monto</dt>
                    <dd>${{ number_format($payment->amount, 2) }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Tipo de Pago</dt>
                    <dd>{{ ucfirst($payment->payment_type) }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">N° Transacción</dt>
                    <dd>{{ $payment->transaction_number }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Observaciones</dt>
                    <dd>{{ $payment->observations ?? '-' }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Estado</dt>
                    <dd>{{ ucfirst($payment->status) }}</dd>
                </div>
            </dl>
            <div class="mt-6 flex flex-wrap gap-2 justify-end">
                @if($payment->status === 'pendiente')
                    <button type="button"
                        onclick="openApproveModal()"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        <i class="fas fa-check mr-1"></i> Aprobar
                    </button>
                    <button type="button"
                        onclick="openRejectModal()"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        <i class="fas fa-times mr-1"></i> Rechazar
                    </button>
                @endif
            </div>
        </div>
    </div>


    <!-- Modal para aprobar pago -->
    <div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-2xl space-y-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                Observaciones para aprobar pago
            </h3>
            <form id="approveForm" method="POST" action="{{ route('payments.approve', $payment) }}">
                @csrf
                @method('POST')
                <div class="mb-3">
                    <label for="approve_observations" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea
                        name="observations"
                        id="approve_observations"
                        class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                        rows="3"
                        placeholder="Ingrese observaciones (opcional)"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeApproveModal()" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Aprobar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal para motivo de rechazo -->
    <div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-2xl space-y-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                Motivo de rechazo
            </h3>
            <form id="rejectForm" method="POST" action="{{ route('payments.reject', $payment) }}">
                @csrf
                <textarea name="rejection_reason" required class="w-full border rounded p-2 mb-4" placeholder="Ingrese el motivo de rechazo"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Rechazar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeRejectModal();
        });
        function openApproveModal() {
            document.getElementById('approveModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.getElementById('approveForm').reset();
        }
        document.getElementById('approveModal').addEventListener('click', function(e) {
            if (e.target === this) closeApproveModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeApproveModal();
        });
    </script>
</x-app-layout>