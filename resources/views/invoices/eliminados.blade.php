<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-trash-restore mr-2"></i> Facturas Eliminadas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            Facturas Eliminadas ({{ $invoices->total() }})
                        </h3>
                        <a href="{{ route('invoices.index') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver a Facturas
                        </a>
                    </div>

                    @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                    @endif

                    {{-- Buscador y cantidad por página --}}
                    <form method="GET" action="{{ route('invoices.eliminados') }}" class="flex flex-col md:flex-row md:items-center gap-4 mb-6">
                        <input
                            type="text"
                            name="search"
                            placeholder="Buscar por número, cliente o vendedor..."
                            value="{{ request('search') }}"
                            class="w-full md:w-1/3 px-4 py-2 border rounded shadow-sm focus:outline-none focus:ring focus:border-indigo-300">
                        <input
                            type="number"
                            name="per_page"
                            min="1"
                            placeholder="Registros por página"
                            value="{{ request('per_page', 10) }}"
                            class="w-full md:w-48 px-4 py-2 border rounded shadow-sm focus:outline-none focus:ring focus:border-indigo-300">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Aplicar
                        </button>
                    </form>


                    @if($invoices->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Número de Factura
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cliente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Creada por
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Eliminada
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($invoices as $invoice)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $invoice->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $invoice->client?->name ?? 'Cliente no disponible' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        S/ {{ number_format($invoice->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $invoice->user?->name ?? 'Usuario no disponible' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $invoice->deleted_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button type="button"
                                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs"
                                            onclick="openRestoreModal({{ $invoice->id }}, '{{ $invoice->invoice_number }}')">
                                            <i class="fas fa-undo mr-1"></i> Restaurar
                                        </button>

                                        <button type="button"
                                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs"
                                            onclick="openForceDeleteModal({{ $invoice->id }}, '{{ $invoice->invoice_number }}')">
                                            <i class="fas fa-trash mr-1"></i> Eliminar Permanente
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-6">
                        {{ $invoices->withQueryString()->links() }}
                    </div>

                    @else
                    <div class="text-center py-12">
                        <i class="fas fa-trash text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay facturas eliminadas</h3>
                        <p class="text-gray-500">Todas las facturas están activas en el sistema.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Restaurar -->
    <div id="restoreModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Icono de restauración -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <i class="fas fa-undo text-green-600 text-xl"></i>
                </div>

                <!-- Título -->
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Restaurar Factura
                    </h3>

                    <!-- Mensaje -->
                    <div class="mt-2 px-2 py-3">
                        <p class="text-sm text-gray-500">
                            ¿Está seguro de que desea restaurar la factura <span id="restoreInvoiceNumber" class="font-medium text-blue-600"></span>?
                        </p>
                        <div class="mt-3 text-left bg-blue-50 border border-blue-200 rounded p-3">
                            <p class="text-xs text-blue-800">
                                <strong>Efectos de la restauración:</strong>
                            </p>
                            <ul class="text-xs text-blue-700 mt-1 list-disc list-inside">
                                <li>La factura volverá a estar activa</li>
                                <li>Se descontará nuevamente del stock</li>
                                <li>Se registrará en el historial de auditoría</li>
                            </ul>
                        </div>
                    </div>

                    <form id="restoreForm" method="POST" class="mt-4">
                        @csrf

                        <div class="mb-4 text-left">
                            <label for="restore_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Motivo de la restauración <span class="text-red-500">*</span>
                            </label>
                            <textarea id="restore_reason" name="reason" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Explique el motivo de la restauración..."></textarea>
                            <div id="restore_reason_error" class="text-red-600 text-sm mt-1 hidden"></div>
                        </div>

                        <div class="mb-4 text-left">
                            <label for="restore_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Tu contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="restore_password" name="password"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ingrese su contraseña para confirmar">
                            <div id="restore_password_error" class="text-red-600 text-sm mt-1 hidden"></div>
                            <p class="mt-1 text-xs text-gray-500">Se requiere tu contraseña para confirmar la restauración.</p>
                        </div>

                        <div class="flex justify-center space-x-3">
                            <button type="button"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded text-sm"
                                onclick="closeRestoreModal()">
                                <i class="fas fa-times mr-1"></i>
                                Cancelar
                            </button>
                            <button type="button"
                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded text-sm"
                                onclick="confirmRestore()">
                                <i class="fas fa-undo mr-1"></i>
                                Sí, Restaurar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Eliminación Permanente -->
    <div id="forceDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Icono de advertencia -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>

                <!-- Título -->
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Eliminación Permanente
                    </h3>

                    <!-- Mensaje -->
                    <div class="mt-2 px-2 py-3">
                        <p class="text-sm text-gray-500">
                            ¿Está ABSOLUTAMENTE seguro de eliminar permanentemente la factura <span id="forceDeleteInvoiceNumber" class="font-medium text-red-600"></span>?
                        </p>
                        <div class="mt-3 text-left bg-red-50 border border-red-200 rounded p-3">
                            <p class="text-xs text-red-800">
                                <strong>¡ADVERTENCIA CRÍTICA!</strong>
                            </p>
                            <ul class="text-xs text-red-700 mt-1 list-disc list-inside">
                                <li>Esta acción elimina permanentemente la factura</li>
                                <li>Los datos NO se pueden recuperar</li>
                                <li>Esta acción es IRREVERSIBLE</li>
                            </ul>
                        </div>
                    </div>

                    <form id="forceDeleteForm" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')

                        <div class="mb-4 text-left">
                            <label for="force_delete_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Motivo de la eliminación permanente <span class="text-red-500">*</span>
                            </label>
                            <textarea id="force_delete_reason" name="reason" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Explique el motivo de la eliminación permanente..."></textarea>
                            <div id="force_delete_reason_error" class="text-red-600 text-sm mt-1 hidden"></div>
                        </div>

                        <div class="mb-4 text-left">
                            <label for="force_delete_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Tu contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="force_delete_password" name="password"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ingrese su contraseña para confirmar">
                            <div id="force_delete_password_error" class="text-red-600 text-sm mt-1 hidden"></div>
                            <p class="mt-1 text-xs text-gray-500">Se requiere tu contraseña para esta acción crítica.</p>
                        </div>

                        <div class="flex justify-center space-x-3">
                            <button type="button"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded text-sm"
                                onclick="closeForceDeleteModal()">
                                <i class="fas fa-times mr-1"></i>
                                Cancelar
                            </button>
                            <button type="button"
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded text-sm"
                                onclick="confirmForceDelete()">
                                <i class="fas fa-trash mr-1"></i>
                                Sí, Eliminar Permanente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentInvoiceId = null;

        function openRestoreModal(invoiceId, invoiceNumber) {
            currentInvoiceId = invoiceId;
            document.getElementById('restoreInvoiceNumber').textContent = invoiceNumber;
            document.getElementById('restoreForm').action = `/invoices/${invoiceId}/restore`;
            document.getElementById('restoreModal').classList.remove('hidden');

            // Limpiar campos y errores
            clearRestoreErrors();
            document.getElementById('restore_reason').value = '';
            document.getElementById('restore_password').value = '';
        }

        function closeRestoreModal() {
            document.getElementById('restoreModal').classList.add('hidden');
            clearRestoreErrors();
        }

        function clearRestoreErrors() {
            // Limpiar errores de validación
            const reasonError = document.getElementById('restore_reason_error');
            const passwordError = document.getElementById('restore_password_error');
            const reasonField = document.getElementById('restore_reason');
            const passwordField = document.getElementById('restore_password');

            reasonError.classList.add('hidden');
            passwordError.classList.add('hidden');
            reasonField.classList.remove('border-red-500');
            passwordField.classList.remove('border-red-500');
        }

        function showRestoreError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '_error');

            field.classList.add('border-red-500');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }

        function confirmRestore() {
            clearRestoreErrors();

            const reason = document.getElementById('restore_reason').value.trim();
            const password = document.getElementById('restore_password').value.trim();
            let hasErrors = false;

            if (!reason || reason.length < 10) {
                showRestoreError('restore_reason', 'El motivo debe tener al menos 10 caracteres.');
                hasErrors = true;
            }

            if (!password) {
                showRestoreError('restore_password', 'La contraseña es obligatoria.');
                hasErrors = true;
            }

            if (!hasErrors) {
                document.getElementById('restoreForm').submit();
            }
        }

        function openForceDeleteModal(invoiceId, invoiceNumber) {
            currentInvoiceId = invoiceId;
            document.getElementById('forceDeleteInvoiceNumber').textContent = invoiceNumber;
            document.getElementById('forceDeleteForm').action = `/invoices/${invoiceId}/force-delete`;
            document.getElementById('forceDeleteModal').classList.remove('hidden');

            // Limpiar campos y errores
            clearForceDeleteErrors();
            document.getElementById('force_delete_reason').value = '';
            document.getElementById('force_delete_password').value = '';
        }

        function closeForceDeleteModal() {
            document.getElementById('forceDeleteModal').classList.add('hidden');
            clearForceDeleteErrors();
        }

        function clearForceDeleteErrors() {
            // Limpiar errores de validación
            const reasonError = document.getElementById('force_delete_reason_error');
            const passwordError = document.getElementById('force_delete_password_error');
            const reasonField = document.getElementById('force_delete_reason');
            const passwordField = document.getElementById('force_delete_password');

            reasonError.classList.add('hidden');
            passwordError.classList.add('hidden');
            reasonField.classList.remove('border-red-500');
            passwordField.classList.remove('border-red-500');
        }

        function showForceDeleteError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '_error');

            field.classList.add('border-red-500');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }

        function confirmForceDelete() {
            clearForceDeleteErrors();

            const reason = document.getElementById('force_delete_reason').value.trim();
            const password = document.getElementById('force_delete_password').value.trim();
            let hasErrors = false;

            if (!reason || reason.length < 10) {
                showForceDeleteError('force_delete_reason', 'El motivo debe tener al menos 10 caracteres.');
                hasErrors = true;
            }

            if (!password) {
                showForceDeleteError('force_delete_password', 'La contraseña es obligatoria.');
                hasErrors = true;
            }

            if (!hasErrors) {
                document.getElementById('forceDeleteForm').submit();
            }
        }

        // Cerrar modales con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRestoreModal();
                closeForceDeleteModal();
            }
        });

        // Cerrar modales al hacer clic fuera
        document.getElementById('restoreModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRestoreModal();
            }
        });

        document.getElementById('forceDeleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeForceDeleteModal();
            }
        });

        // Limpiar errores al escribir - Modal de Restaurar
        document.getElementById('restore_reason').addEventListener('input', function() {
            if (this.value.trim().length >= 10) {
                this.classList.remove('border-red-500');
                document.getElementById('restore_reason_error').classList.add('hidden');
            }
        });

        document.getElementById('restore_password').addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                this.classList.remove('border-red-500');
                document.getElementById('restore_password_error').classList.add('hidden');
            }
        });

        // Limpiar errores al escribir - Modal de Eliminación Permanente
        document.getElementById('force_delete_reason').addEventListener('input', function() {
            if (this.value.trim().length >= 10) {
                this.classList.remove('border-red-500');
                document.getElementById('force_delete_reason_error').classList.add('hidden');
            }
        });

        document.getElementById('force_delete_password').addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                this.classList.remove('border-red-500');
                document.getElementById('force_delete_password_error').classList.add('hidden');
            }
        });
    </script>
</x-app-layout>