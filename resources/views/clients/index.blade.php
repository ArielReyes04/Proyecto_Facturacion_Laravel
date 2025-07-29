<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-users mr-2"></i> Gestión de Clientes
            </h2>
            <div class="flex space-x-2">
                @role('Administrador')
                <a href="{{ route('clients.eliminados') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-trash-alt mr-2"></i> Ver Eliminados
                </a>
                @endrole
                <a href="{{ route('clients.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i> Nuevo Cliente
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Improved Search and Filters -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div id="filterForm" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Search Input -->
                                <div>
                                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-search mr-1 text-gray-500"></i> Buscar Cliente
                                    </label>
                                    <input
                                        type="text"
                                        id="search"
                                        name="search"
                                        placeholder="Buscar por nombre, email o documento..."
                                        value="{{ request('search') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
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
                            </div>
                            
                            <!-- Filter Buttons -->
                            <div class="flex justify-end space-x-2">
                                <button type="button" id="clearFilters" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                                    <i class="fas fa-times mr-2"></i> Limpiar Filtros
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="loading" class="hidden text-center py-8">
                        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-indigo-500 bg-white">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Cargando clientes...
                        </div>
                    </div>

                    <!-- Results Count -->
                    <div class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg mb-4">
                        <i class="fas fa-users mr-1"></i> Total: <span id="totalRecords" class="font-semibold">{{ $clients->total() }}</span> clientes
                    </div>

                    <!-- Clients Table -->
                    <div class="overflow-x-auto" id="tableContainer">
                        @include('clients.partials.clients-table', ['clients' => $clients])
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6" id="paginationContainer">
                        @include('clients.partials.clients-pagination', ['clients' => $clients])
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-2xl space-y-4">
            <h3 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Eliminar Cliente
            </h3>
            <p class="text-sm text-gray-600">
                Está a punto de eliminar el cliente: <strong id="deleteClientName"></strong> (<span id="deleteClientEmail"></span>)
            </p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Motivo de la eliminación <span class="text-red-500">*</span></label>
                    <textarea
                        name="razon"
                        class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                        rows="3"
                        placeholder="Ingrese el motivo de la eliminación..."
                        required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tu contraseña <span class="text-red-500">*</span></label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Escribe tu contraseña para confirmar"
                        class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                        required>
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        onclick="closeDeleteModal()"
                        class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>Eliminar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let searchTimeout;
        let currentPage = 1;

        // Initialize search functionality
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            setupPaginationListeners(); // Initialize pagination on page load
        });

        function setupEventListeners() {
            // Search input with debouncing
            const searchInput = document.getElementById('search');
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentPage = 1;
                    performSearch();
                }, 500);
            });

            // Per page
            const perPageInput = document.getElementById('per_page');
            perPageInput.addEventListener('change', function() {
                currentPage = 1;
                performSearch();
            });

            // Clear filters
            const clearFiltersBtn = document.getElementById('clearFilters');
            clearFiltersBtn.addEventListener('click', function() {
                searchInput.value = '';
                perPageInput.value = '10';
                currentPage = 1;
                performSearch();
            });
        }

        function performSearch() {
            const searchTerm = document.getElementById('search').value;
            const perPage = document.getElementById('per_page').value || 10;
            
            showLoading();
            
            // Build query parameters
            const params = new URLSearchParams({
                ajax: '1',
                page: currentPage,
                per_page: perPage
            });
            
            if (searchTerm) params.append('search', searchTerm);
            
            fetch(`{{ route('clients.index') }}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                updateResults(data);
                
                // Update URL without reloading page
                const url = new URL(window.location);
                if (searchTerm) url.searchParams.set('search', searchTerm);
                else url.searchParams.delete('search');
                
                if (perPage !== '10') url.searchParams.set('per_page', perPage);
                else url.searchParams.delete('per_page');
                
                if (currentPage > 1) url.searchParams.set('page', currentPage);
                else url.searchParams.delete('page');
                
                window.history.replaceState({}, '', url);
            })
            .catch(error => {
                hideLoading();
                console.error('Error en la búsqueda:', error);
                showAlert('Error al realizar la búsqueda. Por favor, inténtelo de nuevo.', 'error');
            });
        }

        function updateResults(data) {
            // Update table
            document.getElementById('tableContainer').innerHTML = data.table;
            
            // Update pagination
            document.getElementById('paginationContainer').innerHTML = data.pagination;
            
            // Update total count
            document.getElementById('totalRecords').textContent = data.total;
            
            // Re-setup pagination event listeners
            setupPaginationListeners();
        }

        function setupPaginationListeners() {
            // Handle pagination clicks
            document.querySelectorAll('[data-page]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(this.getAttribute('data-page'));
                    if (page && page !== currentPage) {
                        currentPage = page;
                        performSearch();
                    }
                });
            });
        }

        function showLoading() {
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('tableContainer').style.opacity = '0.5';
            document.getElementById('paginationContainer').style.opacity = '0.5';
        }

        function hideLoading() {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('tableContainer').style.opacity = '1';
            document.getElementById('paginationContainer').style.opacity = '1';
        }

        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg max-w-sm ${
                type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 
                type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
                'bg-blue-100 border border-blue-400 text-blue-700'
            }`;
            
            alertDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-lg font-bold">×</button>
                </div>
            `;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Delete modal functions (existing functionality)
        function openDeleteModal(clientId, clientName, clientEmail) {
            document.getElementById('deleteClientName').textContent = clientName;
            document.getElementById('deleteClientEmail').textContent = clientEmail;
            document.getElementById('deleteForm').action = `/clients/${clientId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</x-app-layout>