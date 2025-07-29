<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-invoice mr-2"></i> Factura {{ $invoice->invoice_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('invoices.pdf', $invoice) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-file-pdf mr-2"></i> PDF
                </a>
                <form method="POST" action="{{ route('invoices.send-email', $invoice) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-envelope mr-2"></i> Enviar por Email
                    </button>
                </form>
                @if($invoice->isActive() && $invoice->canBeCancelledBy(auth()->user()))
                    <a href="{{ route('invoices.cancel', $invoice) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-times-circle mr-2"></i> Cancelar
                    </a>
                @endif
                @if($invoice->canBeDeletedBy(auth()->user()))
                    <a href="{{ route('invoices.confirm-delete', $invoice) }}" class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-trash mr-2"></i> Eliminar
                    </a>
                @endif
                <a href="{{ route('invoices.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i> Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- Mensajes de éxito y error específicos para esta vista --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 relative" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span class="block sm:inline">{{ session('success') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none';">
                                <i class="fas fa-times"></i>
                            </span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 relative" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span class="block sm:inline">{{ session('error') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none';">
                                <i class="fas fa-times"></i>
                            </span>
                        </div>
                    @endif

                    <!-- Cabecera de la factura -->
                    <div class="mb-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">FACTURA</h1>
                                <p class="text-lg font-medium text-gray-700">{{ $invoice->invoice_number }}</p>
                                <p class="text-sm text-gray-500">{{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $invoice->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $invoice->status == 'active' ? 'ACTIVA' : 'CANCELADA' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Información del cliente y vendedor -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Información del Cliente</h3>
                            <p class="text-sm text-gray-700"><strong>Nombre:</strong> {{ $invoice->client?->name ?? 'Cliente no disponible' }}</p>
                            <p class="text-sm text-gray-700"><strong>Email:</strong> {{ $invoice->client->email }}</p>
                            <p class="text-sm text-gray-700"><strong>Documento:</strong> {{ $invoice->client->document_type }}: {{ $invoice->client->document_number }}</p>
                            @if($invoice->client->phone)
                                <p class="text-sm text-gray-700"><strong>Teléfono:</strong> {{ $invoice->client->phone }}</p>
                            @endif
                            @if($invoice->client->address)
                                <p class="text-sm text-gray-700"><strong>Dirección:</strong> {{ $invoice->client->address }}</p>
                            @endif
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Información del Vendedor</h3>
                            <p class="text-sm text-gray-700"><strong>Nombre:</strong> {{ $invoice->user?->name ?? 'Usuario no disponible' }}</p>
                            <p class="text-sm text-gray-700"><strong>Email:</strong> {{ $invoice->user->email }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Fechas de la Factura</h3>
                            <p class="text-sm text-gray-700"><strong>Fecha de Emisión:</strong> {{ $invoice->issue_date->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-700"><strong>Fecha de Vencimiento:</strong> {{ $invoice->due_date->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-700"><strong>Estado:</strong> 
                                @if($invoice->status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Pagada
                                    </span>
                                @elseif($invoice->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Pendiente
                                    </span>
                                @elseif($invoice->status === 'overdue')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Vencida
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-times-circle mr-1"></i> Cancelada
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Detalle de productos -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Detalle de Productos</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Producto
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Precio Unitario
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Cantidad
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($invoice->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $item->product_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            S/ {{ number_format($item->unit_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            S/ {{ number_format($item->total, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Totales -->
                    <div class="flex justify-end">
                        <div class="w-full max-w-xs">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Subtotal:</span>
                                    <span class="text-sm text-gray-900">S/ {{ number_format($invoice->subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">IVA (15%):</span>
                                    <span class="text-sm text-gray-900">S/ {{ number_format($invoice->tax, 2) }}</span>
                                </div>
                                <div class="border-t pt-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-900">Total:</span>
                                        <span class="text-lg font-bold text-gray-900">S/ {{ number_format($invoice->total, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de cancelación -->
                    @if($invoice->isCancelled())
                        <div class="mt-6 bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <i class="fas fa-exclamation-triangle text-red-400 mr-3"></i>
                                <div>
                                    <h3 class="text-sm font-medium text-red-800">Factura Cancelada</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p><strong>Fecha de cancelación:</strong> {{ $invoice->cancelled_at->format('d/m/Y H:i') }}</p>
                                        <p><strong>Cancelada por:</strong> {{ $invoice->cancelledBy?->name ?? 'Usuario no disponible' }}</p>
                                        <p><strong>Motivo:</strong> {{ $invoice->cancellation_reason }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Información de eliminación -->
                    @if($invoice->trashed())
                        <div class="mt-6 bg-orange-50 border border-orange-200 rounded-md p-4">
                            <div class="flex">
                                <i class="fas fa-trash text-orange-400 mr-3"></i>
                                <div>
                                    <h3 class="text-sm font-medium text-orange-800">Factura Eliminada</h3>
                                    <div class="mt-2 text-sm text-orange-700">
                                        <p><strong>Fecha de eliminación:</strong> {{ $invoice->deleted_at->format('d/m/Y H:i') }}</p>
                                        @if($invoice->deletion_reason)
                                            <p><strong>Motivo:</strong> {{ $invoice->deletion_reason }}</p>
                                        @endif
                                        @if($invoice->deletedBy)
                                            <p><strong>Eliminada por:</strong> {{ $invoice->deletedBy?->name ?? 'Usuario no disponible' }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
