<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-key mr-2"></i> Tokens de Acceso del Cliente: {{ $client->email }}
                </h2>
                <a href="{{ route('clients.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left"></i> Volver a Clientes
                </a>
            </div>
        </x-slot>
        {{-- Alerta de token generado --}}
        @if(session('token_generado'))
            <div class="mb-6">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-sm relative" role="alert">
                    <strong class="font-bold">Token generado exitosamente:</strong>
                    <div class="mt-2 flex items-center gap-2">
                        <code id="token-text" class="bg-gray-100 px-2 py-1 rounded text-sm text-red-600">
                            {{ session('token_generado') }}
                        </code>
                        <button onclick="copiarToken()" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                            <i class="fas fa-copy"></i> Copiar
                        </button> 
                    </div>
                    <p class="text-xs text-red-500 mt-1">¡Cópialo ahora! No volverá a mostrarse.</p>
                </div>
            </div>

            <!-- Notificación flotante -->
            <div id="toast-copiado" class="hidden fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-md z-50 transition-opacity duration-300">
                Token copiado al portapapeles
            </div>
        @endif



        {{-- Formulario para crear token --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-white text-lg font-semibold">Crear Token de Acceso</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('crearTokenAcceso', ['client' => $client->id]) }}" method="post" class="space-y-4">
                    @csrf
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Token</label>
                        <input type="text" name="nombre" id="nombre" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <p class="text-xs text-gray-500 mt-1">Ingrese un nombre identificador para el token.</p>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-plus-circle mr-2"></i> Crear Token
                    </button>
                </form>
            </div>
        </div>

        <!-- Improved Search and Pagination Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 shadow-sm">
            <form id="filterForm" method="GET" action="{{ route('clients.tokens', ['id' => $client->id]) }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-1 text-gray-500"></i> Buscar Token
                        </label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            placeholder="Buscar por nombre de token..."
                            value="{{ request('search') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            autocomplete="off"
                        >
                    </div>

                    <!-- Per Page Selection -->
                    <div>
                        <label for="per_page" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-list mr-1 text-gray-500"></i> Registros por página
                        </label>
                        <input
                            type="number"
                            id="per_page"
                            name="per_page"
                            min="1"
                            max="100"
                            placeholder="10"
                            value="{{ request('per_page', 10) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        >
                    </div>

                    <!-- Submit and Clear Buttons -->
                    <div class="flex items-end space-x-2">
                        <button
                            type="submit"
                            class="bg-indigo-600 text-white px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center w-full"
                        >
                            <i class="fas fa-filter mr-2"></i> Filtrar
                        </button>
                        <button
                            type="button"
                            id="clearFilters"
                            class="bg-gray-500 text-white px-4 py-3 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center w-full"
                        >
                            <i class="fas fa-times mr-2"></i> Limpiar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabla de tokens --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full table-auto text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 uppercase text-xs font-bold text-gray-600">
                        <tr>
                            <th class="px-4 py-2">Usuario</th>
                            <th class="px-4 py-2">Nombre del Token</th>
                            <th class="px-4 py-2">Token</th>
                            <th class="px-4 py-2">Fecha de Creación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tokens as $token)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $client->email }}</td>
                                <td class="px-4 py-2">{{ $token->name }}</td>
                                <td class="px-4 py-2 truncate max-w-xs">
                                    <code class="text-xs text-gray-800">{{ $token->token }}</code>
                                </td>
                                <td class="px-4 py-2">{{ $token->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center px-4 py-3 text-gray-500">No se han encontrado tokens.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Paginación --}}
                <div class="mt-4">
                    {{ $tokens->links() }}
                </div>
            </div>
        </div>

    </div>
    <script>
        function copiarToken() {
            const tokenText = document.getElementById("token-text").innerText;

            navigator.clipboard.writeText(tokenText)
                .then(() => {
                    const toast = document.getElementById("toast-copiado");
                    toast.classList.remove("hidden");
                    toast.classList.add("opacity-100");

                    setTimeout(() => {
                        toast.classList.add("opacity-0");
                        setTimeout(() => toast.classList.add("hidden"), 500);
                    }, 2000);
                })
                .catch(err => {
                    console.error("Error al copiar el token:", err);
                });
        }
        document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('search').value = '';
        document.getElementById('per_page').value = 10;
        // Opcionalmente, puedes hacer submit para recargar con filtros limpios
        document.getElementById('filterForm').submit();
    });
    </script>


</x-app-layout>
