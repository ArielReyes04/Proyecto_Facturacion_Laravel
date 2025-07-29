<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-trash-alt mr-2"></i> Clientes Eliminados
            </h2>
            <a href="{{ route('clients.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left"></i> Volver a Clientes
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($clients->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Eliminación</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($clients as $client)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $client->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $client->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $client->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $client->document_type }}: {{ $client->document_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $client->deleted_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button type="button" class="text-green-600 hover:text-green-900 font-medium" 
                                                            onclick="openRestoreModal({{ $client->id }}, '{{ $client->name }}', '{{ $client->email }}')">
                                                        <i class="fas fa-undo mr-1"></i> Restaurar
                                                    </button>
                                                    <button type="button" class="text-red-600 hover:text-red-900 font-medium" 
                                                            onclick="openForceDeleteModal({{ $client->id }}, '{{ $client->name }}', '{{ $client->email }}')">
                                                        <i class="fas fa-trash mr-1"></i> Eliminar Definitivamente
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $clients->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-info-circle text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500 text-lg">No hay clientes eliminados en el sistema.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Modal -->
    <div id="restoreModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-2xl space-y-4">
            <h3 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-undo text-green-500 mr-2"></i>
                Restaurar Cliente
            </h3>
            <p class="text-sm text-gray-600">
                Está a punto de restaurar el cliente: <strong id="restoreClientName"></strong> (<span id="restoreClientEmail"></span>)
            </p>
            <form id="restoreForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Motivo de la restauración <span class="text-red-500">*</span></label>
                    <textarea
                        name="razon"
                        class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                        rows="3"
                        placeholder="Ingrese el motivo de la restauración..."
                        required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Contraseña de administrador <span class="text-red-500">*</span></label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Escribe tu contraseña"
                        class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                        required>
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        onclick="closeRestoreModal()"
                        class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        <i class="fas fa-undo mr-2"></i>Restaurar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Force Delete Modal -->
    <div id="forceDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-2xl space-y-4">
            <h3 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Eliminar Definitivamente
            </h3>
            <div class="bg-red-50 border border-red-200 rounded p-4">
                <p class="text-sm text-red-700">
                    <strong>¡ATENCIÓN!</strong> Esta acción es irreversible. El cliente <strong id="forceDeleteClientName"></strong> (<span id="forceDeleteClientEmail"></span>) será eliminado permanentemente del sistema.
                </p>
            </div>
            <form id="forceDeleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Motivo de la eliminación definitiva <span class="text-red-500">*</span></label>
                    <textarea
                        name="razon"
                        class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                        rows="3"
                        placeholder="Ingrese el motivo de la eliminación definitiva..."
                        required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Contraseña de administrador <span class="text-red-500">*</span></label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Escribe tu contraseña"
                        class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                        required>
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        onclick="closeForceDeleteModal()"
                        class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>Eliminar Definitivamente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRestoreModal(clientId, clientName, clientEmail) {
            document.getElementById('restoreClientName').textContent = clientName;
            document.getElementById('restoreClientEmail').textContent = clientEmail;
            document.getElementById('restoreForm').action = `/clients/${clientId}/restore`;
            document.getElementById('restoreModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeRestoreModal() {
            document.getElementById('restoreModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openForceDeleteModal(clientId, clientName, clientEmail) {
            document.getElementById('forceDeleteClientName').textContent = clientName;
            document.getElementById('forceDeleteClientEmail').textContent = clientEmail;
            document.getElementById('forceDeleteForm').action = `/clients/${clientId}/force-delete`;
            document.getElementById('forceDeleteModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeForceDeleteModal() {
            document.getElementById('forceDeleteModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Close modals when clicking outside
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

        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRestoreModal();
                closeForceDeleteModal();
            }
        });
    </script>
</x-app-layout>
