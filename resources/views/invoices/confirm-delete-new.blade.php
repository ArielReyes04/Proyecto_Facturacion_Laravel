<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-trash-alt mr-2"></i> Confirmar Eliminación de Factura
            </h2>
            <a href="{{ route('invoices.show', $invoice) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Información de la factura -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Información de la Factura</h3>
                        <p class="text-sm text-gray-700"><strong>Número:</strong> {{ $invoice->invoice_number }}</p>
                        <p class="text-sm text-gray-700"><strong>Cliente:</strong> {{ $invoice->client?->name ?? 'Cliente no disponible' }}</p>
                        <p class="text-sm text-gray-700"><strong>Total:</strong> S/ {{ number_format($invoice->total, 2) }}</p>
                        <p class="text-sm text-gray-700"><strong>Estado:</strong> 
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $invoice->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $invoice->status == 'active' ? 'Activa' : 'Cancelada' }}
                            </span>
                        </p>
                        <p class="text-sm text-gray-700"><strong>Creada por:</strong> {{ $invoice->user?->name ?? 'Usuario no disponible' }}</p>
                        <p class="text-sm text-gray-700"><strong>Fecha:</strong> {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <!-- Advertencia -->
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-red-400 mr-3"></i>
                            <div>
                                <h3 class="text-sm font-medium text-red-800">Advertencia - Eliminación de Factura</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>Al eliminar esta factura:</p>
                                    <ul class="mt-1 list-disc list-inside">
                                        @if($invoice->status === 'active')
                                            <li>Se restituirá el stock de todos los productos</li>
                                        @else
                                            <li>No se restaurará stock (factura ya cancelada)</li>
                                        @endif
                                        <li>La factura se marcará como eliminada (soft delete)</li>
                                        <li>Solo un administrador podrá restaurarla</li>
                                        <li>Debes proporcionar un motivo válido</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($invoice->status === 'active' && $invoice->items->count() > 0)
                    <!-- Productos afectados -->
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                        <h3 class="text-sm font-medium text-blue-800 mb-2">Productos que serán restaurados al stock:</h3>
                        <div class="text-xs text-blue-700">
                            @foreach($invoice->items as $item)
                                <div class="flex justify-between py-1">
                                    <span>{{ $item->product?->name ?? 'Producto no disponible' }}</span>
                                    <span>+{{ $item->quantity }} unidades</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Formulario de eliminación -->
                    <form method="POST" action="{{ route('invoices.confirm-destroy', $invoice) }}" id="deleteForm">
                        @csrf
                        
                        <div class="mb-6">
                            <label for="reason" class="block text-sm font-medium text-gray-700">
                                Motivo de la eliminación <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reason" id="reason" rows="4" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                      placeholder="Explique detalladamente el motivo de la eliminación..." required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Tu contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   placeholder="Ingresa tu contraseña para confirmar" required>
                            <p class="mt-1 text-xs text-gray-500">Se requiere tu contraseña para confirmar la eliminación de la factura.</p>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('invoices.show', $invoice) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="openDeleteModal()">
                                <i class="fas fa-trash-alt mr-2"></i> Confirmar Eliminación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Icono de advertencia -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                
                <!-- Título -->
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Confirmar Eliminación
                    </h3>
                    
                    <!-- Mensaje -->
                    <div class="mt-2 px-2 py-3">
                        <p class="text-sm text-gray-500">
                            ¿Estás seguro de que quieres eliminar esta factura?
                        </p>
                        <p class="text-sm text-red-600 font-medium mt-2">
                            @if($invoice->status === 'active')
                                Esta acción restaurará el stock de todos los productos.
                            @else
                                Esta factura ya está cancelada.
                            @endif
                        </p>
                        <div class="mt-3 text-left bg-yellow-50 border border-yellow-200 rounded p-3">
                            <p class="text-xs text-yellow-800">
                                <strong>Efectos de la eliminación:</strong>
                            </p>
                            <ul class="text-xs text-yellow-700 mt-1 list-disc list-inside">
                                <li>La factura se marcará como eliminada</li>
                                @if($invoice->status === 'active')
                                    <li>Se restaurará automáticamente el stock</li>
                                @endif
                                <li>Se registrará en el historial de auditoría</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex justify-center space-x-3 mt-4">
                        <button type="button" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded text-sm"
                                onclick="closeDeleteModal()">
                            <i class="fas fa-times mr-1"></i>
                            No, Mantener
                        </button>
                        <button type="button" 
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded text-sm"
                                onclick="confirmDelete()">
                            <i class="fas fa-trash-alt mr-1"></i>
                            Sí, Eliminar Factura
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal() {
            // Validar que se haya llenado el motivo antes de abrir el modal
            const reason = document.getElementById('reason').value.trim();
            const password = document.getElementById('password').value.trim();
            
            // Limpiar errores previos
            clearValidationErrors();
            
            if (!reason || reason.length < 10) {
                showValidationError('reason', 'El motivo debe tener al menos 10 caracteres.');
                return;
            }
            
            if (!password) {
                showValidationError('password', 'La contraseña es obligatoria.');
                return;
            }
            
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function confirmDelete() {
            // Verificar una vez más antes de enviar
            const reason = document.getElementById('reason').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!reason || reason.length < 10) {
                closeDeleteModal();
                showValidationError('reason', 'El motivo debe tener al menos 10 caracteres.');
                return;
            }
            
            if (!password) {
                closeDeleteModal();
                showValidationError('password', 'La contraseña es obligatoria.');
                return;
            }
            
            // Enviar el formulario
            document.getElementById('deleteForm').submit();
        }

        function showValidationError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'mt-1 text-sm text-red-600 validation-error';
            errorDiv.textContent = message;
            
            // Añadir clase de error al campo
            field.classList.add('border-red-300');
            
            // Insertar mensaje después del campo
            field.parentNode.insertBefore(errorDiv, field.nextSibling);
            
            // Hacer focus en el campo
            field.focus();
        }

        function clearValidationErrors() {
            // Remover mensajes de error
            document.querySelectorAll('.validation-error').forEach(el => el.remove());
            
            // Remover clases de error
            document.getElementById('reason').classList.remove('border-red-300');
            document.getElementById('password').classList.remove('border-red-300');
        }

        // Cerrar modal si se hace clic fuera de él
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });

        // Limpiar errores al escribir
        document.getElementById('reason').addEventListener('input', function() {
            if (this.value.trim().length >= 10) {
                this.classList.remove('border-red-300');
                const errorEl = this.parentNode.querySelector('.validation-error');
                if (errorEl) errorEl.remove();
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                this.classList.remove('border-red-300');
                const errorEl = this.parentNode.querySelector('.validation-error');
                if (errorEl) errorEl.remove();
            }
        });
    </script>
</x-app-layout>
