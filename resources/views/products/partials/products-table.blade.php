<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Producto
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Descripción
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Precio
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Stock
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Estado
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Acciones
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($products as $product)
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-box text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $product->name }}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">
                <div class="max-w-xs truncate">
                    {{ Str::limit($product->description, 50) ?: 'Sin descripción' }}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex items-center">
                    <i class="fas fa-dollar-sign text-gray-400 mr-2"></i>
                    <span class="font-mono bg-green-100 text-green-800 px-2 py-1 rounded">
                        S/ {{ number_format($product->price, 2) }}
                    </span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex items-center">
                    <i class="fas fa-boxes text-gray-400 mr-2"></i>
                    <span class="px-2 py-1 text-xs font-medium rounded {{ $product->stock < 10 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        {{ $product->stock }} unidades
                    </span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-3 py-1 text-xs font-medium rounded-full inline-flex items-center {{ isset($product->is_active) && $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    <i class="fas {{ isset($product->is_active) && $product->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                    {{ isset($product->is_active) && $product->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                    <a href="{{ route('products.show', $product) }}" 
                       class="text-blue-600 hover:text-blue-900 transition-colors"
                       title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('products.edit', $product) }}" 
                       class="text-indigo-600 hover:text-indigo-900 transition-colors"
                       title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" 
                            class="text-red-600 hover:text-red-900 transition-colors" 
                            onclick="openDeleteModal({{ $product->id }}, '{{ $product->name }}', '{{ number_format($product->price, 2) }}')"
                            title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                <div class="flex flex-col items-center">
                    <i class="fas fa-box text-4xl text-gray-300 mb-4"></i>
                    <p class="text-lg font-medium">No hay productos registrados</p>
                    <p class="text-sm">Los productos aparecerán aquí cuando se registren en el sistema.</p>
                    <a href="{{ route('products.create') }}" 
                       class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i> Crear primer producto
                    </a>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
