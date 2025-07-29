<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard - Sistema de Facturación
        </h2>
    </x-slot>

    <!-- Estilos adicionales para el dashboard -->
    <style>
        .transition-shadow {
            transition: box-shadow 0.15s ease-in-out;
        }
        .transition-colors {
            transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tarjetas de estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @role('Administrador|Ventas')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-invoice text-3xl"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium">Total Facturas</p>
                                <p class="text-3xl font-bold">{{ number_format($stats['total_invoices']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gradient-to-r from-green-500 to-green-600 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-3xl"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium">Facturas Activas</p>
                                <p class="text-3xl font-bold">{{ number_format($stats['active_invoices']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-dollar-sign text-3xl"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium">Ingresos Totales</p>
                                <p class="text-3xl font-bold">S/ {{ number_format($stats['total_revenue'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endrole

                @role('Administrador|Secretario')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gradient-to-r from-purple-500 to-purple-600 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-users text-3xl"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium">Clientes Activos</p>
                                <p class="text-3xl font-bold">{{ number_format($stats['active_clients']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endrole

                @role('Administrador|Bodega')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gradient-to-r from-red-500 to-red-600 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-3xl"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium">Stock Bajo</p>
                                <p class="text-3xl font-bold">{{ number_format($stats['low_stock_products']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endrole
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- Últimas facturas -->
                @role('Administrador|Ventas')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-file-invoice-dollar mr-2"></i> Últimas Facturas
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Número
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Cliente
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estado
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recent_invoices as $invoice)
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
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $invoice->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $invoice->status == 'active' ? 'Activa' : 'Cancelada' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endrole

                <!-- Productos con poco stock -->
                @role('Administrador|Bodega')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-box-open mr-2"></i> Productos con Poco Stock
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Producto
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stock
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Precio
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($low_stock_products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $product->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                            {{ $product->stock }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            S/ {{ number_format($product->price, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endrole

                <!-- Productos más vendidos -->
                @role('Administrador|Ventas')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-trophy mr-2"></i> Productos Más Vendidos
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Producto
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Vendidos
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ingresos
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($top_products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $product->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $product->total_sold }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            S/ {{ number_format($product->total_revenue, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                Ver todos los productos →
                            </a>
                        </div>
                    </div>
                </div>
                @endrole

                <!-- CONTENIDO ESPECÍFICO PARA SECRETARIO -->
                @role('Secretario')
                @if($secretary_data)
                <!-- Panel de métricas adicionales para Secretario -->
                <div class="lg:col-span-2 xl:col-span-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-plus text-indigo-500 text-2xl mr-3"></i>
                                <div>
                                    <p class="text-sm text-indigo-600 font-medium">Nuevos Este Mes</p>
                                    <p class="text-2xl font-bold text-indigo-900">{{ $secretary_data['new_clients_month'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-yellow-500 text-2xl mr-3"></i>
                                <div>
                                    <p class="text-sm text-yellow-600 font-medium">Emails Enviados Hoy</p>
                                    <p class="text-2xl font-bold text-yellow-900">{{ $secretary_data['client_communication_stats']['emails_sent_today'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 text-2xl mr-3"></i>
                                <div>
                                    <p class="text-sm text-red-600 font-medium">Seguimientos Necesarios</p>
                                    <p class="text-2xl font-bold text-red-900">{{ $secretary_data['client_communication_stats']['follow_ups_needed'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel de Próximos Vencimientos -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-calendar-exclamation mr-2"></i> Próximos Vencimientos (7 días)
                        </h3>
                        
                        @if(isset($secretary_data['upcoming_due_dates']) && $secretary_data['upcoming_due_dates']->count() > 0)
                        <div class="space-y-3">
                            @foreach($secretary_data['upcoming_due_dates'] as $invoice)
                            <div class="flex justify-between items-center p-3 border border-orange-200 bg-orange-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Factura #{{ $invoice->invoice_number }}
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $invoice->client?->name ?? 'Cliente no disponible' }}</p>
                                    <p class="text-xs text-orange-600">
                                        Vence: {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : 'No especificado' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900">S/ {{ number_format($invoice->total, 2) }}</p>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-xs text-blue-600 hover:text-blue-900">Ver factura</a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                            <p class="text-gray-500">No hay facturas próximas a vencer</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Panel de Análisis de Ingresos -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-chart-line mr-2"></i> Análisis de Ingresos
                        </h3>
                        
                        <!-- Valor promedio de factura -->
                        <div class="bg-blue-50 p-4 rounded-lg mb-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-blue-600 font-medium">Valor Promedio de Factura</p>
                                    <p class="text-xl font-bold text-blue-900">S/ {{ number_format($secretary_data['average_invoice_value']['current_month'] ?? 0, 2) }}</p>
                                    <p class="text-xs text-gray-600">Este mes</p>
                                </div>
                                <div class="text-right">
                                    @if(isset($secretary_data['average_invoice_value']['percentage_change']) && $secretary_data['average_invoice_value']['percentage_change'] > 0)
                                        <span class="text-green-600 text-sm font-medium">
                                            <i class="fas fa-arrow-up"></i> +{{ $secretary_data['average_invoice_value']['percentage_change'] }}%
                                        </span>
                                    @elseif(isset($secretary_data['average_invoice_value']['percentage_change']) && $secretary_data['average_invoice_value']['percentage_change'] < 0)
                                        <span class="text-red-600 text-sm font-medium">
                                            <i class="fas fa-arrow-down"></i> {{ $secretary_data['average_invoice_value']['percentage_change'] }}%
                                        </span>
                                    @else
                                        <span class="text-gray-600 text-sm font-medium">
                                            <i class="fas fa-minus"></i> 0%
                                        </span>
                                    @endif
                                    <p class="text-xs text-gray-500">vs mes anterior</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tendencia mensual -->
                        <h4 class="text-md font-medium text-gray-800 mb-3">Tendencia de Ingresos (6 meses)</h4>
                        <div class="space-y-2">
                            @if(isset($secretary_data['monthly_revenue_trend']))
                            @foreach($secretary_data['monthly_revenue_trend'] as $trend)
                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($trend->month . '-01')->format('M Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $trend->invoice_count ?? 0 }} facturas</p>
                                </div>
                                <p class="text-sm font-bold text-green-600">S/ {{ number_format($trend->revenue ?? 0, 2) }}</p>
                            </div>
                            @endforeach
                            @else
                            <div class="text-center py-4">
                                <p class="text-gray-500 text-sm">No hay datos de tendencia disponibles</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Panel de Clientes Sin Actividad Reciente -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-user-slash mr-2"></i> Clientes Sin Actividad Reciente
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Clientes sin facturas en los últimos 3 meses</p>
                        
                        <div class="space-y-2">
                            @if(isset($secretary_data['clients_without_recent_activity']))
                            @forelse($secretary_data['clients_without_recent_activity'] as $client)
                            <div class="flex justify-between items-center p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $client->name ?? 'Nombre no disponible' }}</p>
                                    <p class="text-xs text-gray-500">{{ $client->email ?? 'Email no disponible' }}</p>
                                    <p class="text-xs text-orange-600">
                                        Última actividad: Sin facturas recientes
                                    </p>
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('clients.show', $client) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <p class="text-xs text-gray-500 mt-1">Contactar</p>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <i class="fas fa-users text-green-500 text-4xl mb-3"></i>
                                <p class="text-gray-500">Todos los clientes tienen actividad reciente</p>
                            </div>
                            @endforelse
                            @else
                            <div class="text-center py-8">
                                <i class="fas fa-users text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500">No hay datos disponibles</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Panel de Clientes para Secretario -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-users mr-2"></i> Gestión de Clientes
                        </h3>
                        
                        <!-- Estadísticas rápidas de clientes -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-user-plus text-blue-500 text-2xl mr-3"></i>
                                    <div>
                                        <p class="text-sm text-blue-600 font-medium">Nuevos Hoy</p>
                                        <p class="text-2xl font-bold text-blue-900">{{ $secretary_data['new_clients_today'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-week text-green-500 text-2xl mr-3"></i>
                                    <div>
                                        <p class="text-sm text-green-600 font-medium">Esta Semana</p>
                                        <p class="text-2xl font-bold text-green-900">{{ $secretary_data['new_clients_week'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Últimos clientes registrados -->
                        <h4 class="text-md font-medium text-gray-800 mb-3">Últimos Clientes Registrados</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if(isset($secretary_data['recent_clients']))
                                    @forelse($secretary_data['recent_clients'] as $client)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            <a href="{{ route('clients.show', $client) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $client->name ?? 'Nombre no disponible' }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $client->email ?? 'Email no disponible' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $client->created_at ? $client->created_at->format('d/m/Y') : 'Fecha no disponible' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No hay clientes recientes</td>
                                    </tr>
                                    @endforelse
                                    @else
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No hay datos disponibles</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('clients.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                Ver todos los clientes →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Panel de Facturas para Secretario -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-file-alt mr-2"></i> Estado de Facturas
                        </h3>
                        
                        <!-- Alertas de facturas -->
                        <div class="space-y-3 mb-6">
                            @if(isset($secretary_data['pending_invoices']) && $secretary_data['pending_invoices'] > 0)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-yellow-800 font-medium">
                                            {{ $secretary_data['pending_invoices'] ?? 0 }} facturas pendientes de pago
                                        </p>
                                        <a href="{{ route('invoices.index') }}?status=pending" class="text-yellow-700 hover:text-yellow-900 text-xs underline">
                                            Ver facturas pendientes
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(isset($secretary_data['overdue_invoices']) && $secretary_data['overdue_invoices'] > 0)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-times-circle text-red-500 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-red-800 font-medium">
                                            {{ $secretary_data['overdue_invoices'] }} facturas vencidas
                                        </p>
                                        <a href="{{ route('invoices.index') }}?status=overdue" class="text-red-700 hover:text-red-900 text-xs underline">
                                            Ver facturas vencidas
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Resumen de estados -->
                        <h4 class="text-md font-medium text-gray-800 mb-3">Resumen por Estado</h4>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($secretary_data['invoice_status_summary'] as $status => $count)
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-sm text-gray-600">
                                    @if($status === 'paid')
                                        <i class="fas fa-check-circle text-green-500 mr-1"></i> Pagadas
                                    @elseif($status === 'pending')
                                        <i class="fas fa-clock text-yellow-500 mr-1"></i> Pendientes
                                    @elseif($status === 'overdue')
                                        <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i> Vencidas
                                    @else
                                        <i class="fas fa-times-circle text-gray-500 mr-1"></i> Canceladas
                                    @endif
                                </p>
                                <p class="text-lg font-bold text-gray-900">{{ $count }}</p>
                            </div>
                            @endforeach
                        </div>

                        <!-- Top clientes -->
                        <h4 class="text-md font-medium text-gray-800 mb-3 mt-6">Mejores Clientes</h4>
                        <div class="space-y-2">
                            @forelse($secretary_data['top_clients'] as $client)
                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $client->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $client->total_invoices }} facturas</p>
                                </div>
                                <p class="text-sm font-bold text-green-600">S/ {{ number_format($client->total_spent, 2) }}</p>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 text-center py-4">No hay datos de clientes disponibles</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Panel de Resumen y Acciones Rápidas para Secretario -->
                <div class="lg:col-span-2 xl:col-span-3 bg-gradient-to-r from-blue-50 to-purple-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-tasks mr-2"></i> Resumen y Acciones Rápidas
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Tareas Pendientes -->
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="text-md font-medium text-gray-800 mb-3">Tareas Pendientes</h4>
                                <ul class="space-y-2 text-sm">
                                    @if($secretary_data['client_communication_stats']['follow_ups_needed'] > 0)
                                    <li class="flex items-center text-orange-600">
                                        <i class="fas fa-phone mr-2"></i>
                                        {{ $secretary_data['client_communication_stats']['follow_ups_needed'] }} seguimientos necesarios
                                    </li>
                                    @endif
                                    
                                    @if($secretary_data['upcoming_due_dates']->count() > 0)
                                    <li class="flex items-center text-red-600">
                                        <i class="fas fa-calendar-times mr-2"></i>
                                        {{ $secretary_data['upcoming_due_dates']->count() }} facturas próximas a vencer
                                    </li>
                                    @endif
                                    
                                    @if($secretary_data['clients_without_recent_activity']->count() > 0)
                                    <li class="flex items-center text-yellow-600">
                                        <i class="fas fa-user-clock mr-2"></i>
                                        {{ $secretary_data['clients_without_recent_activity']->count() }} clientes inactivos
                                    </li>
                                    @endif
                                    
                                    @if($secretary_data['client_communication_stats']['follow_ups_needed'] == 0 && $secretary_data['upcoming_due_dates']->count() == 0)
                                    <li class="flex items-center text-green-600">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Todas las tareas al día
                                    </li>
                                    @endif
                                </ul>
                            </div>

                            <!-- Acciones Rápidas -->
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="text-md font-medium text-gray-800 mb-3">Acciones Rápidas</h4>
                                <div class="space-y-2">
                                    <a href="{{ route('clients.create') }}" class="block w-full bg-blue-600 text-white text-center py-2 px-3 rounded text-sm hover:bg-blue-700">
                                        <i class="fas fa-user-plus mr-1"></i> Nuevo Cliente
                                    </a>
                                    <a href="{{ route('clients.index') }}" class="block w-full bg-gray-600 text-white text-center py-2 px-3 rounded text-sm hover:bg-gray-700">
                                        <i class="fas fa-users mr-1"></i> Ver Clientes
                                    </a>
                                    <a href="{{ route('invoices.index') }}" class="block w-full bg-green-600 text-white text-center py-2 px-3 rounded text-sm hover:bg-green-700">
                                        <i class="fas fa-file-invoice mr-1"></i> Ver Facturas
                                    </a>
                                </div>
                            </div>

                            <!-- Estadísticas del Día -->
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="text-md font-medium text-gray-800 mb-3">Hoy</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Nuevos clientes:</span>
                                        <span class="font-medium">{{ $secretary_data['new_clients_today'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Emails enviados:</span>
                                        <span class="font-medium">{{ $secretary_data['client_communication_stats']['emails_sent_today'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Clientes activos:</span>
                                        <span class="font-medium text-green-600">{{ $stats['active_clients'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endrole

                <!-- CONTENIDO ESPECÍFICO PARA BODEGA -->
                @role('Bodega')
                @if($warehouse_data)
                <div class="lg:col-span-2 xl:col-span-3">
                    <!-- Header Principal de Bodega -->
                    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-xl shadow-lg p-6 mb-8">
                        <div class="flex items-center">
                            <div class="bg-white/10 backdrop-blur rounded-lg p-3">
                                <i class="fas fa-warehouse text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-2xl font-bold text-white">Control de Inventario</h2>
                                <p class="text-slate-200">Dashboard de gestión de bodega</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjetas de Métricas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Total Productos -->
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 transform hover:scale-105 transition-all duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-3">
                                        <div class="bg-blue-100 rounded-lg p-2">
                                            <i class="fas fa-boxes text-blue-600 text-lg"></i>
                                        </div>
                                        <h3 class="ml-3 text-sm font-semibold text-gray-700">TOTAL PRODUCTOS</h3>
                                    </div>
                                    <div class="text-3xl font-bold text-gray-900 mb-2">
                                        {{ number_format($warehouse_data['inventory_summary']['total_products']) }}
                                    </div>
                                    <div class="text-sm text-green-600 flex items-center">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        En inventario
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Bajo -->
                        <div class="bg-white border border-orange-200 rounded-xl shadow-sm p-6 transform hover:scale-105 transition-all duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-3">
                                        <div class="bg-orange-100 rounded-lg p-2">
                                            <i class="fas fa-exclamation-triangle text-orange-600 text-lg"></i>
                                        </div>
                                        <h3 class="ml-3 text-sm font-semibold text-gray-700">STOCK BAJO</h3>
                                    </div>
                                    <div class="text-3xl font-bold text-orange-600 mb-2">
                                        {{ $warehouse_data['stock_alerts']['low_stock'] }}
                                    </div>
                                    <div class="text-sm text-orange-600 flex items-center">
                                        <i class="fas fa-arrow-down mr-1"></i>
                                        Requiere atención
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sin Stock -->
                        <div class="bg-white border border-red-200 rounded-xl shadow-sm p-6 transform hover:scale-105 transition-all duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-3">
                                        <div class="bg-red-100 rounded-lg p-2">
                                            <i class="fas fa-times-circle text-red-600 text-lg"></i>
                                        </div>
                                        <h3 class="ml-3 text-sm font-semibold text-gray-700">SIN STOCK</h3>
                                    </div>
                                    <div class="text-3xl font-bold text-red-600 mb-2">
                                        {{ $warehouse_data['stock_alerts']['zero_stock'] }}
                                    </div>
                                    <div class="text-sm text-red-600 flex items-center">
                                        <i class="fas fa-ban mr-1"></i>
                                        Estado crítico
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos con stock bajo -->
                @if($warehouse_data['low_stock_products']->count() > 0)
                <div class="lg:col-span-2 xl:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                Productos Requieren Atención
                            </h3>
                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">
                                {{ $warehouse_data['low_stock_products']->count() }} productos
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($warehouse_data['low_stock_products'] as $product)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 text-sm">{{ $product->name }}</h4>
                                        <p class="text-xs text-gray-500 mt-1">SKU: {{ $product->id }}</p>
                                        <p class="text-lg font-bold mt-2 
                                            @if($product->stock == 0) text-red-600
                                            @elseif($product->stock <= 5) text-orange-600
                                            @else text-yellow-600 @endif
                                        ">
                                            {{ $product->stock }} unidades
                                        </p>
                                        <p class="text-sm text-gray-600">S/ {{ number_format($product->price, 2) }}</p>
                                    </div>
                                    <div class="flex-shrink-0 ml-3">
                                        @if($product->stock == 0)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                Agotado
                                            </span>
                                        @elseif($product->stock <= 5)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                                Crítico
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                Bajo
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full 
                                            @if($product->stock == 0) bg-red-500
                                            @elseif($product->stock <= 5) bg-orange-500
                                            @else bg-yellow-500 @endif
                                        " style="width: {{ min(($product->stock / 20) * 100, 100) }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Nivel: {{ number_format(min(($product->stock / 20) * 100, 100), 1) }}%
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-1"></i>
                                Productos que requieren reabastecimiento
                            </div>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Ver todos los productos
                            </a>
                        </div>
                    </div>
                </div>
                @else
                <!-- Estado cuando no hay productos con stock bajo -->
                <div class="lg:col-span-2 xl:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">¡Inventario en Perfecto Estado!</h3>
                        <p class="text-gray-600 max-w-md mx-auto">
                            Todos los productos mantienen niveles de stock adecuados. No se requiere acción inmediata.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                                <i class="fas fa-eye mr-2"></i>
                                Ver inventario completo
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                @endif
                @endrole

            </div>
        </div>
    </div>
</x-app-layout>
