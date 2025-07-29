<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Usuario
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Acción
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tabla
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Registro ID
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Fecha
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Detalles
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-user text-indigo-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $log->user->name ?? 'Usuario eliminado' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $log->user->email ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 text-xs font-medium rounded-full inline-flex items-center
                        {{ $log->action === 'create' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $log->action === 'update' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $log->action === 'delete' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $log->action === 'restore' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $log->action === 'status_change' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $log->action === 'force_delete' ? 'bg-red-200 text-red-900' : '' }}
                        {{ $log->action === 'send_email' ? 'bg-indigo-100 text-indigo-800' : '' }}
                        {{ !in_array($log->action, ['create', 'update', 'delete', 'restore', 'status_change', 'force_delete', 'send_email']) ? 'bg-gray-100 text-gray-800' : '' }}
                    ">
                        @switch($log->action)
                            @case('create')
                                <i class="fas fa-plus mr-1"></i> Crear
                                @break
                            @case('update')
                                <i class="fas fa-edit mr-1"></i> Actualizar
                                @break
                            @case('delete')
                                <i class="fas fa-trash mr-1"></i> Eliminar
                                @break
                            @case('restore')
                                <i class="fas fa-undo mr-1"></i> Restaurar
                                @break
                            @case('status_change')
                                <i class="fas fa-toggle-on mr-1"></i> Cambio de Estado
                                @break
                            @case('force_delete')
                                <i class="fas fa-times-circle mr-1"></i> Eliminación Permanente
                                @break
                            @case('send_email')
                                <i class="fas fa-envelope mr-1"></i> Envío de Email
                                @break
                            @default
                                <i class="fas fa-cog mr-1"></i> {{ ucfirst($log->action) }}
                        @endswitch
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="font-mono bg-gray-100 px-2 py-1 rounded">
                        @switch($log->table_name)
                            @case('users')
                                👥 Usuarios
                                @break
                            @case('clients')
                                🏢 Clientes
                                @break
                            @case('products')
                                📦 Productos
                                @break
                            @case('invoices')
                                🧾 Facturas
                                @break
                            @case('invoice_items')
                                📋 Items de Factura
                                @break
                            @case('audit_logs')
                                📝 Registro de Auditoría
                                @break
                            @case('roles')
                                🔑 Roles
                                @break
                            @case('permissions')
                                🛡️ Permisos
                                @break
                            @case('model_has_roles')
                                👤 Asignación de Roles
                                @break
                            @case('categories')
                                🏷️ Categorías
                                @break
                            @case('suppliers')
                                🚚 Proveedores
                                @break
                            @default
                                📄 {{ $log->table_name ?: 'N/A' }}
                        @endswitch
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $log->record_id ?: 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="flex flex-col">
                        <span class="font-medium">{{ $log->created_at->setTimezone('America/Guayaquil')->format('d/m/Y') }}</span>
                        <span class="text-gray-500 text-xs">{{ $log->created_at->setTimezone('America/Guayaquil')->format('H:i:s') }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <button 
                        onclick="showDetails({{ $log->id }})"
                        class="text-indigo-600 hover:text-indigo-900 font-medium"
                    >
                        <i class="fas fa-eye mr-1"></i> Ver detalles
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-4"></i>
                        <p class="text-lg font-medium">No hay registros de auditoría</p>
                        <p class="text-sm">Los registros de auditoría aparecerán aquí cuando se realicen acciones en el sistema.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
