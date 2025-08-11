<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-yellow-500 leading-tight">
                {{ __('Gestión de Pagos') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('payments.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i> Nuevo Pago
                </a>
                <a href="{{ route('payments.eliminados') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors flex items-center">
                    <i class="fas fa-trash-alt mr-2"></i> Papelera
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Filtros y búsqueda -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <form method="GET" id="filterForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Buscar por cliente -->
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

                        <!-- Registros por página -->
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

                    <!-- Botones -->
                    <div class="flex justify-end space-x-2">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                            <i class="fas fa-filter mr-2"></i> Filtrar
                        </button>
                        <a href="{{ route('payments.index') }}" id="clearFilters" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                            <i class="fas fa-times mr-2"></i> Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>                    

        <h3 class="text-lg font-medium mb-2">Pagos Pendientes</h3>
        @if($pendingPayments->isEmpty())
            <p class="text-gray-500 mb-6">No hay pagos pendientes por validar.</p>
        @else
            @include('payments.partials.pending-table', ['payments' => $pendingPayments])
            {{ $pendingPayments->appends(request()->except('pending_page'))->links('payments.partials.pagination', ['paginator' => $pendingPayments, 'pageName' => 'pending_page']) }}
        @endif

        <h3 class="text-lg font-medium mt-8 mb-2">Historial de Pagos (Aprobados/Rechazados)</h3>
        @if($processedPayments->isEmpty())
            <p class="text-gray-500">No hay pagos procesados.</p>
        @else
            @include('payments.partials.processed-table', ['payments' => $processedPayments])
            {{ $processedPayments->appends(request()->except('processed_page'))->links('payments.partials.pagination', ['paginator' => $processedPayments, 'pageName' => 'processed_page']) }}
        @endif

    </div>
</x-app-layout>
