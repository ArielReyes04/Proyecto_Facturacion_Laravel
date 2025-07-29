<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ESPE FACT</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-indigo-50 via-blue-50 to-cyan-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen">
    
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-xl font-bold text-gray-800">ESPE FACT</span>
                </div>
                <div class="flex space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                Panel de Control
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 px-4 py-2 rounded-lg transition duration-200">
                                Iniciar Sesión
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 to-blue-500/10"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <div class="flex justify-center mb-8">
                    <div class="bg-gradient-to-r from-indigo-500 to-blue-500 p-6 rounded-full shadow-2xl">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                    <span class="text-indigo-600">ESPE FACT</span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto">
                    Plataforma completa para la gestión empresarial con control de clientes, inventario, facturación y auditoría integrada.
                </p>
                
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Ir al Panel de Control
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Comenzar Ahora
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-4">
                    Características Principales
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    Herramientas completas para la gestión empresarial moderna
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="group bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-8 rounded-2xl border border-blue-200 dark:border-blue-800 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-500 rounded-xl mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-800 dark:text-blue-200 mb-3">Gestión de Clientes</h3>
                    <p class="text-blue-600 dark:text-blue-300">Administra toda la información de tus clientes de manera organizada y segura.</p>
                </div>

                <div class="group bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-8 rounded-2xl border border-green-200 dark:border-green-800 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-center w-12 h-12 bg-green-500 rounded-xl mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 21h6" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-green-800 dark:text-green-200 mb-3">Control de Inventario</h3>
                    <p class="text-green-600 dark:text-green-300">Gestiona productos y mantén control total del stock y movimientos.</p>
                </div>

                <div class="group bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 p-8 rounded-2xl border border-purple-200 dark:border-purple-800 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-center w-12 h-12 bg-purple-500 rounded-xl mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-purple-800 dark:text-purple-200 mb-3">Facturación Inteligente</h3>
                    <p class="text-purple-600 dark:text-purple-300">Crea facturas profesionales con generación automática de PDF.</p>
                </div>

                <div class="group bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 p-8 rounded-2xl border border-orange-200 dark:border-orange-800 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-center w-12 h-12 bg-orange-500 rounded-xl mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-orange-800 dark:text-orange-200 mb-3">Seguridad Avanzada</h3>
                    <p class="text-orange-600 dark:text-orange-300">Control de roles, auditoría completa y protección de datos.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Section -->
    <div class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-4">
                    Sistema de Roles
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    Acceso controlado según el rol del usuario
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Administrador</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Acceso total al sistema, gestión de usuarios y configuración.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Secretario</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Gestión completa de clientes y sus datos de contacto.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bodega</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Control de inventario, productos y gestión de stock.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Ventas</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Creación y gestión de facturas, procesamiento de ventas.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-xl font-bold">ESPE FACT</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Solución completa para la gestión empresarial moderna con control de roles, auditoría y seguridad integrada.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Iniciar Sesión</a></li>
                        @auth
                            <li><a href="{{ url('/dashboard') }}" class="hover:text-white transition">Dashboard</a></li>
                        @endauth
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Información</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>© {{ date('Y') }} ESPE FACT</li>
                        <li>Todos los derechos reservados</li>
                        <li>Desarrollado con Laravel</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
