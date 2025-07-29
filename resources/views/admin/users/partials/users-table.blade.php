{{-- Tabla --}}
<div class="overflow-x-auto bg-white shadow rounded">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($users as $user)
            <tr>
                <td class="px-6 py-4">{{ $user->name }}</td>
                <td class="px-6 py-4">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    @if ($user->is_active)
                    <span class="inline-flex px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">
                        Activo
                    </span>
                    @else
                    <span class="inline-flex px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded">
                        Inactivo
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-2">
                        <!-- Botón Editar -->
                        <a href="{{ route('admin.users.edit', $user) }}" 
                           class="inline-flex items-center px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-md transition-colors duration-200">
                            <i class="fas fa-edit mr-1"></i> Editar
                        </a>
                        
                        <!-- Botón Activar/Desactivar -->
                        <form method="POST" action="{{ route('admin.users.updateStatus', $user) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button
                                type="submit"
                                name="open_modal"
                                value="1"
                                class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-md transition-colors duration-200 {{ $user->is_active ? 'bg-orange-500 hover:bg-orange-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                <i class="fas {{ $user->is_active ? 'fa-pause' : 'fa-play' }} mr-1"></i>
                                {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                        
                        <!-- Botón Eliminar (solo si no es el usuario actual) -->
                        @if($user->id !== auth()->id())
                            <button
                                type="button"
                                class="inline-flex items-center px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-md transition-colors duration-200"
                                onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')">
                                <i class="fas fa-trash mr-1"></i> Eliminar
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                    No se encontraron usuarios.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
