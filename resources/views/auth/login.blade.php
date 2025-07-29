<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Iniciar Sesión</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Accede a tu cuenta</p>
    </div>

    {{-- Mensaje general de sesión finalizada --}}
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>{{ session('error') }}</strong>
            </div>
        </div>
    @endif

    {{-- Mensaje de error específico del campo email --}}
    @if ($errors->has('email'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded-lg flex items-center">
            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <span class="text-sm font-medium">{{ $errors->first('email') }}</span>
        </div>
    @endif

    {{-- Mensaje de error específico del campo password --}}
    @if ($errors->has('password'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded-lg flex items-center">
            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span class="text-sm font-medium">{{ $errors->first('password') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                required
                autofocus
                autocomplete="username"
                value="{{ old('email') }}"
                class="mt-1 block w-full rounded-md border {{ $errors->has('email') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-200' }} bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring px-3 py-2"
            >
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
            <input
                id="password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
                class="mt-1 block w-full rounded-md border {{ $errors->has('password') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-200' }} bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring px-3 py-2"
            >
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input
                    type="checkbox"
                    name="remember"
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                >
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Recordarme</span>
            </label>
        </div>

        <!-- Submit -->
        <div>
            <button
                type="submit"
                class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md transition"
            >
                Iniciar Sesión
            </button>
        </div>
    </form>
</x-guest-layout>
