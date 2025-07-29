<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-box mr-2"></i> Gestión de Productos
            </h2>
            <div class="flex space-x-2">
                @role('Administrador')
                <a href="{{ route('products.eliminados') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-trash-alt mr-2"></i> Ver Eliminados
                </a>
                @endrole
                <a href="{{ route('products.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i> Nuevo Producto
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
                                        <i class="fas fa-search mr-1 text-gray-500"></i> Buscar Producto
                                    </label>
                                    <input
                                        type="text"
                                        id="search"
                                        name="search"
                                        placeholder="Buscar por nombre o descripción..."
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
                            Cargando productos...
                        </div>
                    </div>

                    <!-- Results Count -->
                    <div class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg mb-4">
                        <i class="fas fa-box mr-1"></i> Total: <span id="totalRecords" class="font-semibold">{{ $products->total() }}</span> productos
                    </div>

                    <!-- Products Table -->
                    <div class="overflow-x-auto" id="tableContainer">
                        @include('products.partials.products-table', ['products' => $products])
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6" id="paginationContainer">
                        @include('products.partials.products-pagination', ['products' => $products])
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
                Eliminar Producto
            </h3>
            <p class="text-sm text-gray-600">
                Está a punto de eliminar el producto: <strong id="deleteProductName"></strong> (Precio: $<span id="deleteProductPrice"></span>)
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
                        <i class="fas fa-trash mr-2"></i>Eliminar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal(productId, productName, productPrice) {
            document.getElementById('deleteProductName').textContent = productName;
            document.getElementById('deleteProductPrice').textContent = productPrice;
            document.getElementById('deleteForm').action = `/products/${productId}`;
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

        // AJAX functionality for products filtering
        let searchTimeout;
        const searchInput = document.getElementById('search');
        const perPageInput = document.getElementById('per_page');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const loadingIndicator = document.getElementById('loading');
        const tableContainer = document.getElementById('tableContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        const totalRecords = document.getElementById('totalRecords');

        // Search with debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadProducts();
            }, 500);
        });

        // Per page change
        perPageInput.addEventListener('change', function() {
            loadProducts();
        });

        // Clear filters
        clearFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            perPageInput.value = 10;
            loadProducts();
        });

        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('[data-page]')) {
                e.preventDefault();
                const page = e.target.closest('[data-page]').getAttribute('data-page');
                loadProducts(page);
            }
        });

        function loadProducts(page = 1) {
            const params = new URLSearchParams();
            params.append('search', searchInput.value);
            params.append('per_page', perPageInput.value);
            params.append('page', page);
            params.append('ajax', '1');

            showLoading();

            fetch('{{ route("products.index") }}?' + params.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error('Server error:', data.error);
                    console.error('Trace:', data.trace);
                    return;
                }
                
                tableContainer.innerHTML = data.table || '';
                paginationContainer.innerHTML = data.pagination || '';
                totalRecords.textContent = data.total || 0;
                hideLoading();
                
                console.log('Debug info:', data.debug);
                
                // Update URL without page reload
                const url = new URL(window.location);
                url.searchParams.set('search', searchInput.value);
                url.searchParams.set('per_page', perPageInput.value);
                url.searchParams.set('page', page);
                window.history.pushState({}, '', url);
            })
            .catch(error => {
                console.error('Error loading products:', error);
                hideLoading();
                // Show error message to user
                tableContainer.innerHTML = '<div class="text-center py-8 text-red-500">Error al cargar productos. Revisa la consola para más detalles.</div>';
            });
        }

        function showLoading() {
            loadingIndicator.classList.remove('hidden');
            tableContainer.style.opacity = '0.5';
        }

        function hideLoading() {
            loadingIndicator.classList.add('hidden');
            tableContainer.style.opacity = '1';
        }
    </script>
</x-app-layout>