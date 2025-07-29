<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-gray-800">
                ✏️ Editar Usuario
            </h2>
            <a href="{{ route('admin.users') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" id="userEditForm">
                        @csrf
                        @method('PUT')
                        
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
                                        value="{{ old('name', $user->name) }}"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10 @error('name') border-red-300 @enderror"
                                        placeholder="Nombre completo del usuario"
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
                                        value="{{ old('email', $user->email) }}"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10 @error('email') border-red-300 @enderror"
                                        placeholder="correo@ejemplo.com"
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
                                    <i class="fas fa-lock mr-2"></i>Nueva Contraseña
                                    <span class="text-gray-500 text-xs">(Dejar vacío para mantener la actual)</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="password"
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
                                    <i class="fas fa-lock mr-2"></i>Confirmar Nueva Contraseña
                                </label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        name="password_confirmation" 
                                        id="password_confirmation"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10"
                                        placeholder="Confirmar nueva contraseña"
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
                                <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                                <select 
                                    name="role" 
                                    id="role"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('role') border-red-300 @enderror"
                                >
                                    <option value="">Seleccionar rol</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" 
                                            {{ old('role', $userRole?->name) == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Información adicional -->
                            <div class="md:col-span-2 bg-gray-50 p-4 rounded-md">
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Información del usuario</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium">Estado:</span>
                                        <span class="ml-2 px-2 py-1 rounded-full text-xs {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Creado:</span>
                                        <span class="ml-2">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Actualizado:</span>
                                        <span class="ml-2">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                                @if(!$user->is_active && $user->deactivation_reason)
                                    <div class="mt-2">
                                        <span class="font-medium">Razón de desactivación:</span>
                                        <span class="ml-2 text-red-600">{{ $user->deactivation_reason }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('admin.users') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                                Cancelar
                            </a>
                            <button type="submit" id="submitButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-save mr-2"></i> Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('userEditForm');
        const submitButton = document.getElementById('submitButton');
        const userId = {{ $user->id }};
        const validationFields = ['name', 'email']; // password fields are optional in edit
        const fieldValidationState = {};
        
        // Initialize validation state - name and email are required, passwords are optional
        validationFields.forEach(field => {
            fieldValidationState[field] = true; // Start as valid for edit form
        });
        fieldValidationState['password'] = true; // Optional
        fieldValidationState['password_confirmation'] = true; // Optional
        
        let debounceTimers = {};
        
        function debounce(func, wait, fieldName) {
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(debounceTimers[fieldName]);
                    func(...args);
                };
                clearTimeout(debounceTimers[fieldName]);
                debounceTimers[fieldName] = setTimeout(later, wait);
            };
        }
        
        function validateField(fieldName, value, additionalData = {}) {
            const iconContainer = document.getElementById(`${fieldName}-validation-icon`);
            const loadingIcon = document.getElementById(`${fieldName}-loading-icon`);
            const successIcon = document.getElementById(`${fieldName}-success-icon`);
            const errorIcon = document.getElementById(`${fieldName}-error-icon`);
            const errorsDiv = document.getElementById(`${fieldName}-errors`);
            const input = document.getElementById(fieldName);
            
            // Show loading state
            iconContainer.classList.remove('hidden');
            loadingIcon.style.display = 'inline';
            successIcon.style.display = 'none';
            errorIcon.style.display = 'none';
            input.classList.remove('border-green-500', 'border-red-500');
            input.classList.add('border-gray-300');
            errorsDiv.textContent = '';
            
            const formData = new FormData();
            formData.append('field', fieldName);
            formData.append('value', value);
            formData.append('user_id', userId);
            
            // Add additional data
            Object.keys(additionalData).forEach(key => {
                formData.append(key, additionalData[key]);
            });
            
            fetch('{{ route('admin.users.validate-field') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loadingIcon.style.display = 'none';
                
                if (data.valid) {
                    successIcon.style.display = 'inline';
                    input.classList.remove('border-red-500');
                    input.classList.add('border-green-500');
                    errorsDiv.textContent = '';
                    fieldValidationState[fieldName] = true;
                } else {
                    errorIcon.style.display = 'inline';
                    input.classList.remove('border-green-500');
                    input.classList.add('border-red-500');
                    errorsDiv.textContent = data.errors.join(', ');
                    fieldValidationState[fieldName] = false;
                }
                
                updateSubmitButton();
            })
            .catch(error => {
                console.error('Validation error:', error);
                loadingIcon.style.display = 'none';
                errorIcon.style.display = 'inline';
                input.classList.add('border-red-500');
                fieldValidationState[fieldName] = false;
                updateSubmitButton();
            });
        }
        
        function updateSubmitButton() {
            const allValid = Object.values(fieldValidationState).every(valid => valid);
            submitButton.disabled = !allValid;
        }
        
        // Name validation
        const nameInput = document.getElementById('name');
        nameInput.addEventListener('input', debounce(function() {
            const value = this.value.trim();
            if (value.length > 0) {
                validateField('name', value);
            } else {
                resetFieldValidation('name');
            }
        }, 500, 'name'));
        
        // Email validation  
        const emailInput = document.getElementById('email');
        emailInput.addEventListener('input', debounce(function() {
            const value = this.value.trim();
            if (value.length > 0) {
                validateField('email', value);
            } else {
                resetFieldValidation('email');
            }
        }, 500, 'email'));
        
        // Password validation (optional in edit)
        const passwordInput = document.getElementById('password');
        passwordInput.addEventListener('input', debounce(function() {
            const value = this.value;
            if (value.length > 0) {
                validateField('password', value);
                
                // Also validate confirmation if it has value
                const confirmValue = document.getElementById('password_confirmation').value;
                if (confirmValue.length > 0) {
                    validateField('password_confirmation', confirmValue, { password: value });
                }
            } else {
                resetFieldValidation('password', true); // true = optional field
                resetFieldValidation('password_confirmation', true);
            }
        }, 500, 'password'));
        
        // Password confirmation validation
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        passwordConfirmationInput.addEventListener('input', debounce(function() {
            const value = this.value;
            const passwordValue = document.getElementById('password').value;
            if (value.length > 0 && passwordValue.length > 0) {
                validateField('password_confirmation', value, { password: passwordValue });
            } else {
                resetFieldValidation('password_confirmation', true);
            }
        }, 500, 'password_confirmation'));
        
        function resetFieldValidation(fieldName, isOptional = false) {
            const iconContainer = document.getElementById(`${fieldName}-validation-icon`);
            const errorsDiv = document.getElementById(`${fieldName}-errors`);
            const input = document.getElementById(fieldName);
            
            iconContainer.classList.add('hidden');
            input.classList.remove('border-green-500', 'border-red-500');
            input.classList.add('border-gray-300');
            errorsDiv.textContent = '';
            fieldValidationState[fieldName] = isOptional || validationFields.includes(fieldName) ? true : false;
            updateSubmitButton();
        }
        
        // Form submission
        form.addEventListener('submit', function(e) {
            const allValid = Object.values(fieldValidationState).every(valid => valid);
            if (!allValid) {
                e.preventDefault();
                alert('Por favor, complete todos los campos correctamente antes de enviar el formulario.');
            }
        });
        
        // Initial button state
        updateSubmitButton();
    });
    </script>
</x-app-layout>
