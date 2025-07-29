<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-clipboard-list mr-2"></i> Registro de Auditoría
        </h2>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-clipboard-list mr-2 text-indigo-600"></i>
                        Registro de Auditoría
                    </h1>
                    <div class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg">
                        <i class="fas fa-database mr-1"></i> Total: <span class="font-semibold">{{ $logs->total() }}</span> registros
                    </div>
                </div>

                <!-- Improved Search and Filters -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div id="filterForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Search Input -->
                            <div class="md:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-search mr-1 text-gray-500"></i> Buscar
                                </label>
                                <input
                                    type="text"
                                    id="search"
                                    name="search"
                                    placeholder="Buscar por usuario, acción o tabla..."
                                    value="{{ request('search') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                >
                            </div>
                            
                            <!-- Action Filter -->
                            <div>
                                <label for="action_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-filter mr-1 text-gray-500"></i> Filtrar por Acción
                                </label>
                                <select
                                    id="action_filter"
                                    name="action_filter"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                >
                                    <option value="">Todas las acciones</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action_filter') === $action ? 'selected' : '' }}>
                                            @switch($action)
                                                @case('create')
                                                    ✅ Crear
                                                    @break
                                                @case('update')
                                                    ✏️ Actualizar
                                                    @break
                                                @case('delete')
                                                    🗑️ Eliminar
                                                    @break
                                                @case('restore')
                                                    🔄 Restaurar
                                                    @break
                                                @case('status_change')
                                                    🔄 Cambio de Estado
                                                    @break
                                                @case('force_delete')
                                                    ❌ Eliminación Permanente
                                                    @break
                                                @case('send_email')
                                                    📧 Envío de Email
                                                    @break
                                                @default
                                                    📝 {{ ucfirst($action) }}
                                            @endswitch
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Per Page Selection -->
                            <div class="flex items-center space-x-2">
                                <label for="per_page" class="text-sm font-medium text-gray-700">
                                    <i class="fas fa-list mr-1 text-gray-500"></i> Mostrar:
                                </label>
                                <input
                                    type="number"
                                    id="per_page"
                                    name="per_page"
                                    min="1"
                                    max="500"
                                    placeholder="20"
                                    value="{{ request('per_page', 20) }}"
                                    class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-center"
                                >
                                <span class="text-sm text-gray-700">registros por página</span>
                            </div>
                            
                            <!-- Filter Buttons -->
                            <div class="flex space-x-2">
                                <button type="button" id="clearFilters" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                                    <i class="fas fa-times mr-2"></i> Limpiar Filtros
                                </button>
                            </div>
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
                        Cargando...
                    </div>
                </div>

                <!-- Results Count -->
                <div class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg mb-4">
                    <i class="fas fa-database mr-1"></i> Total: <span id="totalRecords" class="font-semibold">{{ $logs->total() }}</span> registros
                </div>

                <!-- Audit Logs Table -->
                <div class="overflow-x-auto" id="tableContainer">
                    @include('admin.partials.audit-logs-table', ['logs' => $logs])
                </div>

                <!-- Pagination -->
                <div class="mt-6" id="paginationContainer">
                    @include('admin.partials.audit-logs-pagination', ['logs' => $logs])
                </div>


<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Detalles de Auditoría</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="modalContent" class="p-6">
            <!-- Content will be loaded here -->
        </div>
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

            // Action filter
            const actionFilter = document.getElementById('action_filter');
            actionFilter.addEventListener('change', function() {
                currentPage = 1;
                performSearch();
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
                actionFilter.value = '';
                perPageInput.value = '20';
                currentPage = 1;
                performSearch();
            });
        }

        function performSearch() {
            const searchTerm = document.getElementById('search').value;
            const actionFilter = document.getElementById('action_filter').value;
            const perPage = document.getElementById('per_page').value || 20;
            
            showLoading();
            
            // Build query parameters
            const params = new URLSearchParams({
                ajax: '1',
                page: currentPage,
                per_page: perPage
            });
            
            if (searchTerm) params.append('search', searchTerm);
            if (actionFilter) params.append('action_filter', actionFilter);
            
            fetch(`{{ route('admin.audit-logs') }}?${params.toString()}`, {
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
                
                if (actionFilter) url.searchParams.set('action_filter', actionFilter);
                else url.searchParams.delete('action_filter');
                
                if (perPage !== '20') url.searchParams.set('per_page', perPage);
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

        // Function to translate field names to Spanish
        function translateFieldName(fieldName) {
            const translations = {
                // Campos comunes
                'id': '🆔 ID',
                'name': '📝 Nombre',
                'email': '📧 Email',
                'password': '🔒 Contraseña',
                'created_at': '📅 Fecha de Creación',
                'updated_at': '📅 Fecha de Actualización',
                'deleted_at': '🗑️ Fecha de Eliminación',
                'is_active': '✅ Activo',
                
                // Usuarios
                'deactivation_reason': '❌ Razón de Desactivación',
                'email_verified_at': '✅ Email Verificado',
                'remember_token': '🔑 Token de Recordar',
                
                // Clientes
                'phone': '📞 Teléfono',
                'address': '🏠 Dirección',
                'document_type': '📄 Tipo de Documento',
                'document_number': '🔢 Número de Documento',
                
                // Productos
                'description': '📋 Descripción',
                'price': '💰 Precio',
                'stock': '📦 Stock',
                'category': '🏷️ Categoría',
                'supplier': '🚚 Proveedor',
                
                // Facturas
                'invoice_number': '🧾 Número de Factura',
                'client_id': '🏢 Cliente ID',
                'user_id': '👤 Usuario ID',
                'issue_date': '📅 Fecha de Emisión',
                'due_date': '📅 Fecha de Vencimiento',
                'subtotal': '💰 Subtotal',
                'tax': '🏛️ Impuesto',
                'total': '💰 Total',
                'status': '📊 Estado',
                'cancelled_at': '❌ Fecha de Cancelación',
                'cancelled_by': '👤 Cancelado por',
                'cancellation_reason': '❌ Razón de Cancelación',
                'deletion_reason': '🗑️ Razón de Eliminación',
                'deleted_by': '👤 Eliminado por',
                
                // Items de factura
                'invoice_id': '🧾 Factura ID',
                'product_id': '📦 Producto ID',
                'product_name': '📦 Nombre del Producto',
                'unit_price': '💰 Precio Unitario',
                'quantity': '🔢 Cantidad',
                
                // Auditoría específica
                'razon': '📝 Razón',
                'reason': '📝 Razón',
                'admin_user': '👑 Usuario Administrador',
                'restored_by': '🔄 Restaurado por',
                'action_by': '👤 Acción realizada por',
                
                // Otros campos comunes
                'notes': '📝 Notas',
                'comments': '💬 Comentarios',
                'type': '📋 Tipo',
                'code': '🏷️ Código',
                'slug': '🔗 Slug',
                'image': '🖼️ Imagen',
                'file': '📎 Archivo',
                'url': '🔗 URL',
                'sort_order': '🔢 Orden',
                'priority': '⭐ Prioridad',
                'enabled': '✅ Habilitado',
                'disabled': '❌ Deshabilitado',
                'visible': '👁️ Visible',
                'hidden': '🙈 Oculto'
            };
            
            return translations[fieldName] || `📄 ${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')}`;
        }

        function showDetails(logId) {
            fetch(`{{ route('admin.audit-logs') }}/${logId}/details`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Create modal content
                const modalContent = `
                    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="detailsModal">
                        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                            <div class="mt-3">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <i class="fas fa-info-circle mr-2 text-indigo-600"></i>
                                        Detalles del Registro de Auditoría
                                    </h3>
                                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Usuario:</label>
                                            <p class="mt-1 text-sm text-gray-900">${data.user_name || 'Usuario eliminado'}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Email:</label>
                                            <p class="mt-1 text-sm text-gray-900">${data.user_email || 'N/A'}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Acción:</label>
                                            <p class="mt-1 text-sm text-gray-900">${data.action_label}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Tabla:</label>
                                            <p class="mt-1 text-sm text-gray-900">${data.table_label}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">ID del Registro:</label>
                                            <p class="mt-1 text-sm text-gray-900">${data.record_id || 'N/A'}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Fecha y Hora:</label>
                                            <p class="mt-1 text-sm text-gray-900">${data.formatted_date}</p>
                                        </div>
                                    </div>
                                    
                                    ${data.old_values ? `
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Valores Anteriores:</label>
                                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 space-y-1">
                                                ${Object.entries(data.old_values).map(([key, value]) => {
                                                    const translatedKey = translateFieldName(key);
                                                    return `<div class="flex"><span class="font-medium mr-2">${translatedKey}:</span><span>${value}</span></div>`;
                                                }).join('')}
                                            </div>
                                        </div>
                                    ` : ''}
                                    
                                    ${data.new_values ? `
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Valores Nuevos:</label>
                                            <div class="bg-green-50 border border-green-200 rounded-lg p-3 space-y-1">
                                                ${Object.entries(data.new_values).map(([key, value]) => {
                                                    const translatedKey = translateFieldName(key);
                                                    return `<div class="flex"><span class="font-medium mr-2">${translatedKey}:</span><span>${value}</span></div>`;
                                                }).join('')}
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                                
                                <div class="flex justify-end mt-6">
                                    <button onclick="closeModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.insertAdjacentHTML('beforeend', modalContent);
            })
            .catch(error => {
                console.error('Error al cargar detalles:', error);
                showAlert('Error al cargar los detalles del registro.', 'error');
            });
        }

        function closeModal() {
            const modal = document.getElementById('detailsModal');
            if (modal) {
                modal.remove();
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('detailsModal');
            if (modal && event.target === modal) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
