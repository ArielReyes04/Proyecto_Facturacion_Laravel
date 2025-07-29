<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-gray-800">
                👥 Administrar Usuarios
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i> Crear Usuario
                </a>
                <a href="{{ route('admin.users.eliminados') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-trash-alt mr-2"></i> Ver Eliminados
                </a>
            </div>
        </div>
    </x-slot>

    <div
        x-data="{ showModal: {{ session('show_modal') || $errors->any() ? 'true' : 'false' }} }"
        x-init="
            if(showModal){document.body.classList.add('overflow-hidden')}
            $watch('showModal', value => {
                if(value){
                    document.body.classList.add('overflow-hidden')
                } else {
                    document.body.classList.remove('overflow-hidden')
                }
            })
        "
        @keydown.escape.window="showModal=false"
    >
        {{-- Filtros de búsqueda --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <form id="userFilterForm" method="GET" action="{{ route('admin.users') }}" class="flex flex-col md:flex-row md:items-end gap-4">
                    <div class="flex-1">
                        <label for="userSearch" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-search text-gray-500 mr-1"></i>Buscar Usuarios
                        </label>
                        <input
                            type="text"
                            name="search"
                            id="userSearch"
                            placeholder="Buscar por nombre o email..."
                            value="{{ request('search') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="w-full md:w-48">
                        <label for="userPerPage" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-list text-gray-500 mr-1"></i>Registros por página
                        </label>
                        <input
                            type="number"
                            name="per_page"
                            id="userPerPage"
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
                    <span id="userResultCount">Total: {{ $users->total() }} usuarios</span>
                </div>
            </div>

            {{-- Loading indicator --}}
            <div id="userLoadingIndicator" class="hidden text-center py-4">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-500"></i>
                <p class="text-gray-500 mt-2">Cargando usuarios...</p>
            </div>

            @if(session('status'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('status') }}
            </div>
            @endif

            {{-- Table container --}}
            <div id="userTableContainer">
                @include('admin.users.partials.users-table')
            </div>

            {{-- Pagination container --}}
            <div id="userPaginationContainer" class="mt-4">
                @include('admin.users.partials.users-pagination')
            </div>
        </div>

        {{-- Modal --}}
        @if(session('show_modal') || $errors->any())
        <div
            x-show="showModal"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
            <div @click.away="showModal=false" class="bg-white w-full max-w-lg p-6 rounded-lg shadow-2xl space-y-4">
                <h3 class="text-xl font-semibold text-gray-800">Confirmar cambio de estado del usuario</h3>
                <p class="text-sm text-gray-600">Por favor indica el motivo y confirma con tu contraseña.</p>
                <form method="POST" action="{{ route('admin.users.updateStatus', old('user_id')) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ old('is_active') }}">
                    <input type="hidden" name="user_id" value="{{ old('user_id') }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Motivo del cambio</label>
                        <textarea
                            name="reason"
                            class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                            rows="3">{{ old('reason') }}</textarea>
                        @error('reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Contraseña de administrador</label>
                        <input
                            type="password"
                            name="password"
                            placeholder="Escribe tu contraseña"
                            class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300">
                        @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            @click="showModal=false"
                            class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-2xl space-y-4">
            <h3 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Eliminar Usuario
            </h3>
            <p class="text-sm text-gray-600">
                Está a punto de eliminar el usuario: <strong id="deleteUserName"></strong> (<span id="deleteUserEmail"></span>)
            </p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Motivo de la eliminación</label>
                    <textarea
                        name="razon"
                        class="w-full mt-1 border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300"
                        rows="3"
                        placeholder="Ingrese el motivo de la eliminación..."
                        required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Contraseña de administrador</label>
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
                        onclick="closeDeleteModal()"
                        class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>Eliminar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('userFilterForm');
        const searchInput = document.getElementById('userSearch');
        const perPageInput = document.getElementById('userPerPage');
        const clearButton = document.getElementById('clearFilters');
        const tableContainer = document.getElementById('userTableContainer');
        const paginationContainer = document.getElementById('userPaginationContainer');
        const loadingIndicator = document.getElementById('userLoadingIndicator');
        const resultCount = document.getElementById('userResultCount');
        
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
            
            fetch(`{{ route('admin.users') }}?${params.toString()}`, {
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
                    alert('Error al cargar los usuarios: ' + data.error);
                    return;
                }
                
                // Update table and pagination
                tableContainer.innerHTML = data.table;
                paginationContainer.innerHTML = data.pagination;
                
                // Update result count
                if (resultCount) {
                    resultCount.textContent = `Total: ${data.total} usuarios`;
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
                alert('Error de conexión al cargar los usuarios');
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
                            alert('Error al cargar los usuarios: ' + data.error);
                            return;
                        }
                        
                        tableContainer.innerHTML = data.table;
                        paginationContainer.innerHTML = data.pagination;
                        
                        // Update result count
                        if (resultCount) {
                            resultCount.textContent = `Total: ${data.total} usuarios`;
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
                        alert('Error de conexión al cargar los usuarios');
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
        
        // Prevent default form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
    });
    </script>

    <script>
        function openDeleteModal(userId, userName, userEmail) {
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteUserEmail').textContent = userEmail;
            document.getElementById('deleteForm').action = `/admin/users/${userId}`;
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
