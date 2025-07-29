<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" class="flex items-center text-indigo-600 font-bold text-lg">
                    <i class="fas fa-receipt mr-2"></i> Sistema de Facturación
                </a>
                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('dashboard') }}"
                       class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                    
                    @role('Administrador')
                        <a href="{{ route('clients.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('clients.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">
                            <i class="fas fa-users mr-1"></i> Clientes
                        </a>
                        <a href="{{ route('products.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('products.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">
                            <i class="fas fa-box mr-1"></i> Productos
                        </a>
                        <a href="{{ route('invoices.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('invoices.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">
                            <i class="fas fa-file-invoice mr-1"></i> Facturas
                        </a>
                        <a href="{{ route('admin.users') }}"
                           class="text-sm font-medium {{ request()->routeIs('admin.users') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">
                            <i class="fas fa-user-cog mr-1"></i> Usuarios
                        </a>
                        <a href="{{ route('admin.audit-logs') }}"
                           class="text-sm font-medium {{ request()->routeIs('admin.audit-logs') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">
                            <i class="fas fa-clipboard-list mr-1"></i> Auditorías
                        </a>
                    @endrole

                    @role('Secretario')
                        <a href="{{ route('clients.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('clients.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">
                            <i class="fas fa-users mr-1"></i> Clientes
                        </a>
                    @endrole

                    @role('Bodega')
                        <a href="{{ route('products.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('products.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">
                            <i class="fas fa-box mr-1"></i> Productos
                        </a>
                    @endrole

                    @role('Ventas')
                        <a href="{{ route('invoices.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('invoices.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">
                            <i class="fas fa-file-invoice mr-1"></i> Facturas
                        </a>
                    @endrole
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-800">
                    {{ Auth::user()->name }}
                    @if(Auth::user()->hasRole('Administrador'))
                        <span class="text-red-600">(Administrador)</span>
                    @elseif(Auth::user()->hasRole('Secretario'))
                        <span class="text-blue-600">(Secretario)</span>
                    @elseif(Auth::user()->hasRole('Bodega'))
                        <span class="text-green-600">(Bodega)</span>
                    @elseif(Auth::user()->hasRole('Ventas'))
                        <span class="text-purple-600">(Ventas)</span>
                    @endif
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-red-600">
                        <i class="fas fa-sign-out-alt mr-1"></i> Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
