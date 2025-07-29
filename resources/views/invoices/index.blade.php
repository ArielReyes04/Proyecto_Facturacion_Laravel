<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-invoice mr-2"></i> Gestión de Facturas
            </h2>
            <div class="flex space-x-2">
                @if(auth()->user()->hasRole('Administrador'))
                <a href="{{ route('invoices.eliminados') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-trash-alt mr-2"></i> Ver Eliminadas
                </a>
                @endif
                <a href="{{ route('invoices.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i> Nueva Factura
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- Mensajes de éxito y error --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 relative" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span class="block sm:inline">{{ session('success') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none';">
                                <i class="fas fa-times"></i>
                            </span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 relative" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span class="block sm:inline">{{ session('error') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none';">
                                <i class="fas fa-times"></i>
                            </span>
                        </div>
                    @endif

                    {{-- Filtros de búsqueda --}}
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <form id="invoiceFilterForm" method="GET" action="{{ route('invoices.index') }}" class="flex flex-col md:flex-row md:items-end gap-4">
                            <div class="flex-1">
                                <label for="invoiceSearch" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-search text-gray-500 mr-1"></i>Buscar Facturas
                                </label>
                                <input
                                    type="text"
                                    name="search"
                                    id="invoiceSearch"
                                    placeholder="Buscar por número, cliente o vendedor..."
                                    value="{{ request('search') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="w-full md:w-48">
                                <label for="invoicePerPage" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-list text-gray-500 mr-1"></i>Registros por página
                                </label>
                                <input
                                    type="number"
                                    name="per_page"
                                    id="invoicePerPage"
                                    min="1"
                                    max="100"
                                    placeholder="Registros por página"
                                    value="{{ request('per_page', 10) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="flex gap-2">
                                <button 
                                    type="button" 
                                    id="clearFilters"
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-3 rounded-md transition-colors duration-200"
                                    title="Limpiar Filtros">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </form>
                        
                        {{-- Contador de resultados --}}
                        <div class="mt-3 text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            <span id="invoiceResultCount">Total: {{ $invoices->total() }} facturas</span>
                        </div>
                    </div>

                    {{-- Loading indicator --}}
                    <div id="invoiceLoadingIndicator" class="hidden text-center py-4">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-500"></i>
                        <p class="text-gray-500 mt-2">Cargando facturas...</p>
                    </div>

                    {{-- Table container --}}
                    <div id="invoiceTableContainer">
                        @include('invoices.partials.invoices-table')
                    </div>

                    {{-- Pagination container --}}
                    <div id="invoicePaginationContainer" class="mt-6">
                        @include('invoices.partials.invoices-pagination')
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('invoiceFilterForm');
        const searchInput = document.getElementById('invoiceSearch');
        const perPageInput = document.getElementById('invoicePerPage');
        const clearButton = document.getElementById('clearFilters');
        const tableContainer = document.getElementById('invoiceTableContainer');
        const paginationContainer = document.getElementById('invoicePaginationContainer');
        const loadingIndicator = document.getElementById('invoiceLoadingIndicator');
        const resultCount = document.getElementById('invoiceResultCount');
        
        let debounceTimer;
        
        // Debounce function for search input
        function debounce(func, wait) {
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(debounceTimer);
                    func(...args);
                };
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(later, wait);
            };
        }
        
        // Function to perform AJAX request
        function performSearch() {
            const formData = new FormData(form);
            formData.append('ajax', '1');
            
            const params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    params.append(key, value);
                }
            }
            
            // Show loading indicator
            loadingIndicator.classList.remove('hidden');
            tableContainer.style.opacity = '0.5';
            
            fetch(`{{ route('invoices.index') }}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error('Server error:', data.error);
                    alert('Error al cargar las facturas: ' + data.error);
                    return;
                }
                
                // Update table and pagination
                tableContainer.innerHTML = data.table;
                paginationContainer.innerHTML = data.pagination;
                
                // Update result count
                if (resultCount) {
                    resultCount.textContent = `Total: ${data.total} facturas`;
                }
                
                // Update URL without page reload
                const url = new URL(window.location);
                if (searchInput.value.trim()) {
                    url.searchParams.set('search', searchInput.value.trim());
                } else {
                    url.searchParams.delete('search');
                }
                
                if (perPageInput.value && perPageInput.value !== '10') {
                    url.searchParams.set('per_page', perPageInput.value);
                } else {
                    url.searchParams.delete('per_page');
                }
                
                window.history.pushState({}, '', url);
                
                // Re-attach pagination event listeners
                attachPaginationListeners();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al cargar las facturas');
            })
            .finally(() => {
                // Hide loading indicator
                loadingIndicator.classList.add('hidden');
                tableContainer.style.opacity = '1';
            });
        }
        
        // Function to attach event listeners to pagination links
        function attachPaginationListeners() {
            const paginationLinks = paginationContainer.querySelectorAll('a[href]');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    
                    // Preserve current search and per_page values
                    if (searchInput.value.trim()) {
                        url.searchParams.set('search', searchInput.value.trim());
                    }
                    if (perPageInput.value) {
                        url.searchParams.set('per_page', perPageInput.value);
                    }
                    url.searchParams.set('ajax', '1');
                    
                    // Show loading indicator
                    loadingIndicator.classList.remove('hidden');
                    tableContainer.style.opacity = '0.5';
                    
                    fetch(url.toString(), {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Server error:', data.error);
                            alert('Error al cargar las facturas: ' + data.error);
                            return;
                        }
                        
                        tableContainer.innerHTML = data.table;
                        paginationContainer.innerHTML = data.pagination;
                        
                        // Update result count
                        if (resultCount) {
                            resultCount.textContent = `Total: ${data.total} facturas`;
                        }
                        
                        // Update URL
                        const newUrl = new URL(this.href);
                        if (searchInput.value.trim()) {
                            newUrl.searchParams.set('search', searchInput.value.trim());
                        }
                        if (perPageInput.value && perPageInput.value !== '10') {
                            newUrl.searchParams.set('per_page', perPageInput.value);
                        }
                        newUrl.searchParams.delete('ajax');
                        window.history.pushState({}, '', newUrl);
                        
                        // Re-attach listeners
                        attachPaginationListeners();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error de conexión al cargar las facturas');
                    })
                    .finally(() => {
                        loadingIndicator.classList.add('hidden');
                        tableContainer.style.opacity = '1';
                    });
                });
            });
        }
        
        // Clear filters function
        function clearFilters() {
            searchInput.value = '';
            perPageInput.value = '10';
            
            // Update URL to remove parameters
            const url = new URL(window.location);
            url.searchParams.delete('search');
            url.searchParams.delete('per_page');
            window.history.pushState({}, '', url);
            
            // Perform search to refresh the table
            performSearch();
        }
        
        // Initial attachment of pagination listeners
        attachPaginationListeners();
        
        // Event listeners for form inputs
        searchInput.addEventListener('input', debounce(performSearch, 500));
        perPageInput.addEventListener('change', performSearch);
        clearButton.addEventListener('click', clearFilters);
        
        // Prevent default form submission only for the filter form
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
        
        // Ensure email forms are not intercepted and can submit normally
        document.addEventListener('submit', function(e) {
            if (e.target.classList.contains('email-form')) {
                // Allow email forms to submit normally
                return true;
            }
        });
    });
    </script>
</x-app-layout>