<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-user-edit mr-2"></i> Editar Cliente
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
                    <form method="POST" action="{{ route('clients.update', $client) }}" id="clientForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-user mr-1 text-gray-500"></i> Nombre Completo
                                </label>
                                <div class="relative">
                                    <input type="text" name="name" id="name" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" 
                                           value="{{ old('name', $client->name) }}" required>
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
                                           value="{{ old('email', $client->email) }}" required>
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
                                           value="{{ old('phone', $client->phone) }}"
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
                                    <option value="CE" {{ old('document_type', $client->document_type) == 'CE' ? 'selected' : '' }}>Cédula (10 dígitos)</option>
                                    <option value="RUC" {{ old('document_type', $client->document_type) == 'RUC' ? 'selected' : '' }}>RUC (13 dígitos)</option>
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
                                           value="{{ old('document_number', $client->document_number) }}" required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <div id="document_number-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="document_number-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="document_number-error-icon" style="display: none;"></i>
                                            <i class="fas fa-spinner fa-spin text-gray-500" id="document_number-loading-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="document_number-errors" class="mt-2 text-sm text-red-600"></div>
                                <div id="document_number-help" class="mt-1 text-xs text-gray-500"></div>
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
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">{{ old('address', $client->address) }}</textarea>
                                    <div class="absolute top-2 right-2">
                                        <div id="address-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="address-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="address-error-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="address-errors" class="mt-2 text-sm text-red-600"></div>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" id="submitBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-save mr-2"></i> Actualizar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let validationTimeouts = {};
        let fieldValidationStatus = {
            name: true, // Start as true since field has existing value
            email: true, // Start as true since field has existing value
            phone: true, // Phone is optional
            document_number: true, // Start as true since field has existing value
            address: true // Address is optional
        };

        const clientId = {{ $client->id }};

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

            // Document type change
            const documentTypeSelect = document.getElementById('document_type');
            documentTypeSelect.addEventListener('change', function() {
                updateDocumentHelp();
                const documentNumberInput = document.getElementById('document_number');
                if (documentNumberInput.value) {
                    validateField('document_number', documentNumberInput.value);
                }
            });

            // Document number validation
            const documentNumberInput = document.getElementById('document_number');
            documentNumberInput.addEventListener('input', function() {
                validateField('document_number', this.value);
            });

            // Address validation
            const addressInput = document.getElementById('address');
            addressInput.addEventListener('input', function() {
                validateField('address', this.value);
            });

            // Initial document help update
            updateDocumentHelp();
        }

        function validateField(fieldName, value) {
            clearTimeout(validationTimeouts[fieldName]);
            
            showLoadingIcon(fieldName);
            
            validationTimeouts[fieldName] = setTimeout(() => {
                const documentType = document.getElementById('document_type').value;
                
                fetch('{{ route("clients.validate-field") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        field: fieldName,
                        value: value,
                        document_type: documentType,
                        client_id: clientId // Include client ID for edit validation
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingIcon(fieldName);
                    showValidationResult(fieldName, data.valid, data.errors);
                    fieldValidationStatus[fieldName] = data.valid;
                    updateSubmitButton();
                })
                .catch(error => {
                    hideLoadingIcon(fieldName);
                    console.error('Error validating field:', error);
                });
            }, 500);
        }

        function showLoadingIcon(fieldName) {
            const container = document.getElementById(`${fieldName}-validation-icon`);
            const loadingIcon = document.getElementById(`${fieldName}-loading-icon`);
            const successIcon = document.getElementById(`${fieldName}-success-icon`);
            const errorIcon = document.getElementById(`${fieldName}-error-icon`);

            container.classList.remove('hidden');
            loadingIcon.style.display = 'block';
            successIcon.style.display = 'none';
            errorIcon.style.display = 'none';
        }

        function hideLoadingIcon(fieldName) {
            const loadingIcon = document.getElementById(`${fieldName}-loading-icon`);
            loadingIcon.style.display = 'none';
        }

        function showValidationResult(fieldName, isValid, errors) {
            const container = document.getElementById(`${fieldName}-validation-icon`);
            const successIcon = document.getElementById(`${fieldName}-success-icon`);
            const errorIcon = document.getElementById(`${fieldName}-error-icon`);
            const errorsContainer = document.getElementById(`${fieldName}-errors`);
            const input = document.getElementById(fieldName);

            container.classList.remove('hidden');

            if (isValid) {
                successIcon.style.display = 'block';
                errorIcon.style.display = 'none';
                errorsContainer.innerHTML = '';
                input.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                input.classList.add('border-green-300', 'focus:border-green-500', 'focus:ring-green-500');
            } else {
                successIcon.style.display = 'none';
                errorIcon.style.display = 'block';
                errorsContainer.innerHTML = errors.map(error => `<span class="block">${error}</span>`).join('');
                input.classList.remove('border-green-300', 'focus:border-green-500', 'focus:ring-green-500');
                input.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
            }
        }

        function updateDocumentHelp() {
            const documentType = document.getElementById('document_type').value;
            const helpElement = document.getElementById('document_number-help');
            
            switch (documentType) {
                case 'DNI':
                    helpElement.textContent = 'Ingrese 8 dígitos (ej: 12345678)';
                    break;
                case 'CE':
                    helpElement.textContent = 'Ingrese 10 dígitos (ej: 1234567890)';
                    break;
                case 'RUC':
                    helpElement.textContent = 'Ingrese 13 dígitos (ej: 1234567890123)';
                    break;
                case 'Pasaporte':
                    helpElement.textContent = 'Ingrese entre 6 y 12 caracteres alfanuméricos (ej: ABC123456)';
                    break;
                default:
                    helpElement.textContent = '';
            }
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            const isFormValid = Object.values(fieldValidationStatus).every(status => status === true);
            
            submitBtn.disabled = !isFormValid;
            
            if (isFormValid) {
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.add('hover:bg-blue-700');
            } else {
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.remove('hover:bg-blue-700');
            }
        }

        // Prevent form submission if validation fails
        document.getElementById('clientForm').addEventListener('submit', function(e) {
            const isFormValid = Object.values(fieldValidationStatus).every(status => status === true);
            if (!isFormValid) {
                e.preventDefault();
                alert('Por favor, corrija los errores en el formulario antes de continuar.');
            }
        });
    </script>
</x-app-layout>
