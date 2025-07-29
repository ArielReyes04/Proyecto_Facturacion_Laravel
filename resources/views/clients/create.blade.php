<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-user-plus mr-2"></i> Nuevo Cliente
            </h2>
            <a href="{{ route('clients.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('clients.store') }}" id="clientForm">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-user mr-1 text-gray-500"></i> Nombre Completo
                                </label>
                                <div class="relative">
                                    <input type="text" name="name" id="name" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" 
                                           value="{{ old('name') }}" required>
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
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-envelope mr-1 text-gray-500"></i> Email
                                </label>
                                <div class="relative">
                                    <input type="email" name="email" id="email" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" 
                                           value="{{ old('email') }}" required>
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

                            <!-- Teléfono -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-phone mr-1 text-gray-500"></i> Teléfono
                                </label>
                                <div class="relative">
                                    <input type="text" name="phone" id="phone" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" 
                                           value="{{ old('phone') }}"
                                           placeholder="Solo números, máximo 10 dígitos"
                                           maxlength="10"
                                           pattern="[0-9]{1,10}">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <div id="phone-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="phone-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="phone-error-icon" style="display: none;"></i>
                                            <i class="fas fa-spinner fa-spin text-gray-500" id="phone-loading-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="phone-errors" class="mt-2 text-sm text-red-600"></div>
                                <p class="mt-1 text-sm text-gray-500">Solo números, máximo 10 dígitos</p>
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipo de documento -->
                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-id-card mr-1 text-gray-500"></i> Tipo de Documento
                                </label>
                                <select name="document_type" id="document_type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" 
                                        required>
                                    <option value="">Seleccionar...</option>
                                    <option value="CE" {{ old('document_type') == 'CE' ? 'selected' : '' }}>Cédula (10 dígitos)</option>
                                    <option value="RUC" {{ old('document_type') == 'RUC' ? 'selected' : '' }}>RUC (13 dígitos)</option>
                                </select>
                                @error('document_type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Número de documento -->
                            <div>
                                <label for="document_number" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-hashtag mr-1 text-gray-500"></i> Número de Documento
                                </label>
                                <div class="relative">
                                    <input type="text" name="document_number" id="document_number" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" 
                                           value="{{ old('document_number') }}" required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <div id="document_number-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="document_number-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="document_number-error-icon" style="display: none;"></i>
                                            <i class="fas fa-spinner fa-spin text-gray-500" id="document_number-loading-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="document_number-errors" class="mt-2 text-sm text-red-600"></div>
                                @error('document_number')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dirección -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-map-marker-alt mr-1 text-gray-500"></i> Dirección
                                </label>
                                <div class="relative">
                                    <textarea name="address" id="address" rows="3" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">{{ old('address') }}</textarea>
                                    <div class="absolute top-3 right-0 flex items-center pr-3">
                                        <div id="address-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="address-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="address-error-icon" style="display: none;"></i>
                                            <i class="fas fa-spinner fa-spin text-gray-500" id="address-loading-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="address-errors" class="mt-2 text-sm text-red-600"></div>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('clients.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </a>
                            <button type="submit" id="submitButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                <i class="fas fa-save mr-2"></i> Guardar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validaciones y funcionalidad
        let validationStates = {
            name: false,
            email: false,
            phone: true, // Phone is optional
            document_number: false,
            address: true // Address is optional
        };

        let debounceTimers = {};
        
        document.addEventListener('DOMContentLoaded', function() {
            setupValidation();
            updateSubmitButton();
        });

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

            // Phone validation
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function() {
                validateField('phone', this.value);
            });

            // Document number validation
            const documentNumberInput = document.getElementById('document_number');
            documentNumberInput.addEventListener('input', function() {
                const documentType = document.getElementById('document_type').value;
                validateField('document_number', this.value, {document_type: documentType});
            });

            // Document type change validation
            const documentTypeSelect = document.getElementById('document_type');
            documentTypeSelect.addEventListener('change', function() {
                const documentNumber = document.getElementById('document_number').value;
                if (documentNumber) {
                    validateField('document_number', documentNumber, {document_type: this.value});
                }
            });

            // Address validation
            const addressInput = document.getElementById('address');
            addressInput.addEventListener('input', function() {
                validateField('address', this.value);
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

                fetch('{{ route("clients.validate-field") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        showValidationState(fieldName, 'success');
                        validationStates[fieldName] = true;
                    } else {
                        showValidationState(fieldName, 'error', data.errors);
                        validationStates[fieldName] = false;
                    }
                    updateSubmitButton();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showValidationState(fieldName, 'error', ['Error de validación']);
                    validationStates[fieldName] = false;
                    updateSubmitButton();
                });
            }, 500); // 500ms debounce
        }

        function showValidationState(fieldName, state, errors = []) {
            const successIcon = document.getElementById(`${fieldName}-success-icon`);
            const errorIcon = document.getElementById(`${fieldName}-error-icon`);
            const loadingIcon = document.getElementById(`${fieldName}-loading-icon`);
            const errorsDiv = document.getElementById(`${fieldName}-errors`);
            const validationIcon = document.getElementById(`${fieldName}-validation-icon`);
            
            // Hide all icons
            successIcon.style.display = 'none';
            errorIcon.style.display = 'none';
            loadingIcon.style.display = 'none';
            validationIcon.classList.add('hidden');

            switch (state) {
                case 'loading':
                    loadingIcon.style.display = 'block';
                    validationIcon.classList.remove('hidden');
                    errorsDiv.innerHTML = '';
                    break;
                case 'success':
                    successIcon.style.display = 'block';
                    validationIcon.classList.remove('hidden');
                    errorsDiv.innerHTML = '';
                    break;
                case 'error':
                    errorIcon.style.display = 'block';
                    validationIcon.classList.remove('hidden');
                    errorsDiv.innerHTML = errors.map(error => `<p>${error}</p>`).join('');
                    break;
            }
        }

        function updateSubmitButton() {
            const submitButton = document.getElementById('submitButton');
            const allValid = Object.values(validationStates).every(state => state === true);
            
            submitButton.disabled = !allValid;
            
            if (allValid) {
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                submitButton.classList.add('hover:bg-blue-700');
            } else {
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                submitButton.classList.remove('hover:bg-blue-700');
            }
        }
    </script>
</x-app-layout>
