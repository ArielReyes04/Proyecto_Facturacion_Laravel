<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-gray-800">
                👤 Crear Usuario
            </h2>
            <a href="{{ route('admin.users') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Mostrar errores de validación -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Error en la validación</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.users.store') }}" id="userCreateForm">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div class="validation-field">
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-user mr-2"></i>Nombre completo *
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        name="name" 
                                        id="name"
                                        value="{{ old('name') }}"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10 @error('name') border-red-300 @enderror"
                                        placeholder="Ingrese el nombre completo del usuario"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <div id="name-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="name-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="name-error-icon" style="display: none;"></i>
                                            <i class="fas fa-spinner fa-spin text-gray-500" id="name-loading-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="name-errors" class="mt-2 text-sm text-red-600"></div>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="validation-field">
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-envelope mr-2"></i>Correo electrónico *
                                </label>
                                <div class="relative">
                                    <input 
                                        type="email" 
                                        name="email" 
                                        id="email"
                                        value="{{ old('email') }}"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10 @error('email') border-red-300 @enderror"
                                        placeholder="usuario@empresa.com"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <div id="email-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="email-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="email-error-icon" style="display: none;"></i>
                                            <i class="fas fa-spinner fa-spin text-gray-500" id="email-loading-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="email-errors" class="mt-2 text-sm text-red-600"></div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contraseña -->
                            <div class="validation-field">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-lock mr-2"></i>Contraseña *
                                </label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="password"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10 @error('password') border-red-300 @enderror"
                                        placeholder="Mínimo 8 caracteres, 1 mayúscula, 1 número"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <div id="password-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="password-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="password-error-icon" style="display: none;"></i>
                                            <i class="fas fa-spinner fa-spin text-gray-500" id="password-loading-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="password-errors" class="mt-2 text-sm text-red-600"></div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="validation-field">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-lock mr-2"></i>Confirmar contraseña *
                                </label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        name="password_confirmation" 
                                        id="password_confirmation"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10"
                                        placeholder="Confirmar contraseña"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <div id="password_confirmation-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="password_confirmation-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="password_confirmation-error-icon" style="display: none;"></i>
                                            <i class="fas fa-spinner fa-spin text-gray-500" id="password_confirmation-loading-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="password_confirmation-errors" class="mt-2 text-sm text-red-600"></div>
                            </div>

                            <!-- Rol -->
                            <div class="md:col-span-2">
                                <label for="role" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-user-tag mr-2"></i>Rol *
                                </label>
                                <select 
                                    name="role" 
                                    id="role"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('role') border-red-300 @enderror"
                                >
                                    <option value="">Seleccionar rol</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="mt-8 flex items-center justify-end">
                            <a href="{{ route('admin.users') }}" 
                               class="mr-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" 
                                    id="submitButton"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-save mr-2"></i>
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validaciones y funcionalidad
        let validationStates = {
            name: false,
            email: false,
            password: false,
            password_confirmation: false
        };

        let debounceTimers = {};
        
        setupValidation();
        updateSubmitButton();

        function setupValidation() {
            // Name validation
            const nameInput = document.getElementById('name');
            nameInput.addEventListener('input', function() {
                validateField('name', this.value);
            });

            // Email validation
            const emailInput = document.getElementById('email');
            emailInput.addEventListener('input', function() {
                validateField('email', this.value);
            });

            // Password validation
            const passwordInput = document.getElementById('password');
            passwordInput.addEventListener('input', function() {
                validateField('password', this.value);
                
                // Also re-validate confirmation if it has content
                const confirmValue = document.getElementById('password_confirmation').value;
                if (confirmValue) {
                    validateField('password_confirmation', confirmValue, {password: this.value});
                }
            });

            // Password confirmation validation
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            passwordConfirmationInput.addEventListener('input', function() {
                const passwordValue = document.getElementById('password').value;
                validateField('password_confirmation', this.value, {password: passwordValue});
            });

            // Role selection validation
            const roleSelect = document.getElementById('role');
            roleSelect.addEventListener('change', function() {
                updateSubmitButton();
            });
        }

        function validateField(fieldName, value, extraData = {}) {
            // Clear existing timer
            if (debounceTimers[fieldName]) {
                clearTimeout(debounceTimers[fieldName]);
            }

            // Show loading state
            showValidationState(fieldName, 'loading');

            // Set new timer
            debounceTimers[fieldName] = setTimeout(() => {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const formData = new FormData();
                formData.append('field', fieldName);
                formData.append('value', value);
                formData.append('_token', csrfToken);
                
                // Add extra data
                Object.keys(extraData).forEach(key => {
                    formData.append(key, extraData[key]);
                });

                fetch('{{ route('admin.users.validate-field') }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        showValidationState(fieldName, 'success');
                        validationStates[fieldName] = true;
                    } else {
                        showValidationState(fieldName, 'error', data.errors.join(', '));
                        validationStates[fieldName] = false;
                    }
                    updateSubmitButton();
                })
                .catch(error => {
                    console.error('Validation error:', error);
                    showValidationState(fieldName, 'error', 'Error de validación');
                    validationStates[fieldName] = false;
                    updateSubmitButton();
                });
            }, 500);
        }

        function showValidationState(fieldName, state, message = '') {
            const iconContainer = document.getElementById(`${fieldName}-validation-icon`);
            const loadingIcon = document.getElementById(`${fieldName}-loading-icon`);
            const successIcon = document.getElementById(`${fieldName}-success-icon`);
            const errorIcon = document.getElementById(`${fieldName}-error-icon`);
            const errorsDiv = document.getElementById(`${fieldName}-errors`);
            const input = document.getElementById(fieldName);

            // Hide all icons first
            iconContainer.classList.add('hidden');
            loadingIcon.style.display = 'none';
            successIcon.style.display = 'none';
            errorIcon.style.display = 'none';
            
            // Reset input border
            input.classList.remove('border-green-500', 'border-red-500');
            input.classList.add('border-gray-300');

            if (state === 'loading') {
                iconContainer.classList.remove('hidden');
                loadingIcon.style.display = 'inline';
                errorsDiv.textContent = '';
            } else if (state === 'success') {
                iconContainer.classList.remove('hidden');
                successIcon.style.display = 'inline';
                input.classList.remove('border-gray-300');
                input.classList.add('border-green-500');
                errorsDiv.textContent = '';
            } else if (state === 'error') {
                iconContainer.classList.remove('hidden');
                errorIcon.style.display = 'inline';
                input.classList.remove('border-gray-300');
                input.classList.add('border-red-500');
                errorsDiv.textContent = message;
            }
        }

        function updateSubmitButton() {
            const submitButton = document.getElementById('submitButton');
            const roleSelected = document.getElementById('role').value !== '';
            const allFieldsValid = Object.values(validationStates).every(valid => valid === true);
            
            submitButton.disabled = !(allFieldsValid && roleSelected);
        }

        // Form submission validation
        document.getElementById('userCreateForm').addEventListener('submit', function(e) {
            const allFieldsValid = Object.values(validationStates).every(valid => valid === true);
            const roleSelected = document.getElementById('role').value !== '';
            
            if (!allFieldsValid || !roleSelected) {
                e.preventDefault();
                alert('Por favor, complete todos los campos correctamente antes de enviar el formulario.');
            }
        });
    });
    </script>
</x-app-layout>
