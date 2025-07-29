<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-edit mr-2"></i> Editar Producto
            </h2>
            <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('products.update', $product) }}" id="productForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-box mr-1 text-gray-500"></i> Nombre del Producto
                                </label>
                                <div class="relative">
                                    <input type="text" name="name" id="name" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" 
                                           value="{{ old('name', $product->name) }}" required>
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

                            <!-- Precio -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-dollar-sign mr-1 text-gray-500"></i> Precio (S/)
                                </label>
                                <div class="relative">
                                    <input type="text" name="price" id="price" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" 
                                           value="{{ old('price', $product->price) }}" required
                                           placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <div id="price-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="price-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="price-error-icon" style="display: none;"></i>
                                            <i class="fas fa-spinner fa-spin text-gray-500" id="price-loading-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="price-errors" class="mt-2 text-sm text-red-600"></div>
                                <small class="text-gray-500">Ingresa números con decimales (puedes usar coma o punto)</small>
                                @error('price')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Stock -->
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-boxes mr-1 text-gray-500"></i> Stock Actual
                                </label>
                                <div class="relative">
                                    <input type="number" name="stock" id="stock" min="0" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" 
                                           value="{{ old('stock', $product->stock) }}" required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <div id="stock-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="stock-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="stock-error-icon" style="display: none;"></i>
                                            <i class="fas fa-spinner fa-spin text-gray-500" id="stock-loading-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="stock-errors" class="mt-2 text-sm text-red-600"></div>
                                @error('stock')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-align-left mr-1 text-gray-500"></i> Descripción
                                </label>
                                <div class="relative">
                                    <textarea name="description" id="description" rows="4" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                                              placeholder="Descripción detallada del producto...">{{ old('description', $product->description) }}</textarea>
                                    <div class="absolute top-2 right-2">
                                        <div id="description-validation-icon" class="hidden">
                                            <i class="fas fa-check text-green-500" id="description-success-icon" style="display: none;"></i>
                                            <i class="fas fa-times text-red-500" id="description-error-icon" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="description-errors" class="mt-2 text-sm text-red-600"></div>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" id="submitBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-save mr-2"></i> Actualizar Producto
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
            price: true, // Start as true since field has existing value
            stock: true, // Start as true since field has existing value
            description: true // Description is optional
        };

        const productId = {{ $product->id }};

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

            // Price validation with comma/dot conversion
            const priceInput = document.getElementById('price');
            priceInput.addEventListener('input', function() {
                // Replace comma with dot for validation
                let value = this.value.replace(',', '.');
                this.value = value;
                validateField('price', value);
            });

            // Stock validation
            const stockInput = document.getElementById('stock');
            stockInput.addEventListener('input', function() {
                validateField('stock', this.value);
            });

            // Description validation (optional)
            const descriptionInput = document.getElementById('description');
            descriptionInput.addEventListener('input', function() {
                validateField('description', this.value);
            });
        }

        function validateField(fieldName, value) {
            clearTimeout(validationTimeouts[fieldName]);
            
            showLoadingIcon(fieldName);
            
            validationTimeouts[fieldName] = setTimeout(() => {
                fetch('{{ route("products.validate-field") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        field: fieldName,
                        value: value,
                        product_id: productId // Include product ID for edit validation
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

            if (container && loadingIcon) {
                container.classList.remove('hidden');
                loadingIcon.style.display = 'block';
                if (successIcon) successIcon.style.display = 'none';
                if (errorIcon) errorIcon.style.display = 'none';
            }
        }

        function hideLoadingIcon(fieldName) {
            const loadingIcon = document.getElementById(`${fieldName}-loading-icon`);
            if (loadingIcon) {
                loadingIcon.style.display = 'none';
            }
        }

        function showValidationResult(fieldName, isValid, errors) {
            const container = document.getElementById(`${fieldName}-validation-icon`);
            const successIcon = document.getElementById(`${fieldName}-success-icon`);
            const errorIcon = document.getElementById(`${fieldName}-error-icon`);
            const errorsContainer = document.getElementById(`${fieldName}-errors`);
            const input = document.getElementById(fieldName);

            if (!container || !input) return;

            container.classList.remove('hidden');

            if (isValid) {
                if (successIcon) successIcon.style.display = 'block';
                if (errorIcon) errorIcon.style.display = 'none';
                if (errorsContainer) errorsContainer.innerHTML = '';
                input.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                input.classList.add('border-green-300', 'focus:border-green-500', 'focus:ring-green-500');
            } else {
                if (successIcon) successIcon.style.display = 'none';
                if (errorIcon) errorIcon.style.display = 'block';
                if (errorsContainer && errors) {
                    errorsContainer.innerHTML = errors.map(error => `<span class="block">${error}</span>`).join('');
                }
                input.classList.remove('border-green-300', 'focus:border-green-500', 'focus:ring-green-500');
                input.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
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
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const isFormValid = Object.values(fieldValidationStatus).every(status => status === true);
            if (!isFormValid) {
                e.preventDefault();
                alert('Por favor, corrija los errores en el formulario antes de continuar.');
            }
        });
    </script>
</x-app-layout>
