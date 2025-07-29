<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-invoice-dollar mr-2"></i> Nueva Factura
            </h2>
            <a href="{{ route('invoices.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
                        @csrf
                        
                        <!-- Información del cliente -->
                        <div class="mb-6">
                            <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                            <select name="client_id" id="client_id" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    required>
                                <option value="">Seleccionar cliente...</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} - {{ $client->document_type }}: {{ $client->document_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Productos -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Productos</h3>
                                <button type="button" id="addProduct" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-plus mr-2"></i> Agregar Producto
                                </button>
                            </div>
                            
                            <div id="productList" class="space-y-4">
                                <!-- Los productos se agregarán aquí dinámicamente -->
                            </div>
                            
                            @error('products')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Totales -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center text-sm mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotal">S/ 0.00</span>
                            </div>
                            <div class="flex justify-between items-center text-sm mb-2">
                                <span>IGV (15%):</span>
                                <span id="tax">S/ 0.00</span>
                            </div>
                            <div class="flex justify-between items-center text-lg font-bold">
                                <span>Total:</span>
                                <span id="total">S/ 0.00</span>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-save mr-2"></i> Crear Factura
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let productIndex = 0;
        const products = @json($products);

        document.getElementById('addProduct').addEventListener('click', function() {
            addProductRow();
        });

        function addProductRow() {
            const productList = document.getElementById('productList');
            const productRow = document.createElement('div');
            productRow.className = 'border border-gray-300 rounded p-4 product-row';
            productRow.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Producto</label>
                        <select name="products[${productIndex}][id]" class="product-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Seleccionar producto...</option>
                            ${products.map(product => `
                                <option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}">
                                    ${product.name} - Stock: ${product.stock} - S/ ${parseFloat(product.price).toFixed(2)}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio</label>
                        <input type="number" class="unit-price mt-1 block w-full rounded-md border-gray-300 shadow-sm" step="0.01" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                        <input type="number" name="products[${productIndex}][quantity]" class="quantity mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" required>
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="remove-product bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            productList.appendChild(productRow);
            
            // Configurar event listeners para este producto
            setupProductEvents(productRow);
            
            productIndex++;
        }

        function setupProductEvents(productRow) {
            const productSelect = productRow.querySelector('.product-select');
            const unitPriceInput = productRow.querySelector('.unit-price');
            const quantityInput = productRow.querySelector('.quantity');
            const removeButton = productRow.querySelector('.remove-product');
            
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price') || 0;
                const stock = selectedOption.getAttribute('data-stock') || 0;
                
                unitPriceInput.value = parseFloat(price).toFixed(2);
                quantityInput.max = stock;
                quantityInput.value = '';
                
                calculateTotals();
            });
            
            quantityInput.addEventListener('input', function() {
                const stock = parseInt(productSelect.options[productSelect.selectedIndex].getAttribute('data-stock') || 0);
                if (parseInt(this.value) > stock) {
                    alert(`Stock insuficiente. Disponible: ${stock}`);
                    this.value = stock;
                }
                calculateTotals();
            });
            
            removeButton.addEventListener('click', function() {
                productRow.remove();
                calculateTotals();
            });
        }

        function calculateTotals() {
            let subtotal = 0;
            
            document.querySelectorAll('.product-row').forEach(row => {
                const unitPrice = parseFloat(row.querySelector('.unit-price').value || 0);
                const quantity = parseInt(row.querySelector('.quantity').value || 0);
                subtotal += unitPrice * quantity;
            });
            
            const tax = subtotal * 0.18;
            const total = subtotal + tax;
            
            document.getElementById('subtotal').textContent = 'S/ ' + subtotal.toFixed(2);
            document.getElementById('tax').textContent = 'S/ ' + tax.toFixed(2);
            document.getElementById('total').textContent = 'S/ ' + total.toFixed(2);
        }

        // Agregar primera fila de producto
        addProductRow();
    </script>
</x-app-layout>
