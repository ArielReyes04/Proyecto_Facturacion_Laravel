<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Product;
use App\Models\AuditLog;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\CancelInvoiceRequest;
use App\Http\Requests\DeleteInvoiceRequest;
use App\Http\Requests\RestoreInvoiceRequest;
use App\Http\Requests\ForceDeleteInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\InvoiceMail;


class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'user'])
            ->whereHas('client')
            ->whereHas('user');

        // Búsqueda por número, cliente o vendedor
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Registros por página
        $perPage = $request->input('per_page', 10);
        $perPage = max(1, min(100, (int) $perPage)); // Limit between 1 and 100

        $invoices = $query->latest()->paginate($perPage)->withQueryString();

        // Handle AJAX requests
        if ($request->ajax() || $request->has('ajax')) {
            try {
                $tableHtml = view('invoices.partials.invoices-table', compact('invoices'))->render();
                $paginationHtml = view('invoices.partials.invoices-pagination', compact('invoices'))->render();
                
                return response()->json([
                    'table' => $tableHtml,
                    'pagination' => $paginationHtml,
                    'total' => $invoices->total(),
                    'current_page' => $invoices->currentPage(),
                    'last_page' => $invoices->lastPage(),
                    'debug' => [
                        'search' => $request->input('search'),
                        'per_page' => $perPage,
                        'count' => $invoices->count()
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        }

        return view('invoices.index', compact('invoices'));
    }


    public function create()
    {
        $clients = Client::where('is_active', true)->get();
        $products = Product::where('is_active', true)->where('stock', '>', 0)->get();

        return view('invoices.create', compact('clients', 'products'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // Verificar stock disponible
            foreach ($validated['products'] as $productData) {
                $product = Product::findOrFail($productData['id']);
                if (!$product->hasStock($productData['quantity'])) {
                    return back()->withErrors([
                        'products' => "Stock insuficiente para el producto: {$product->name}. Stock disponible: {$product->stock}"
                    ]);
                }
            }

            // Crear factura
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'client_id' => $validated['client_id'],
                'user_id' => Auth::id(),
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
                'status' => 'pendiente',
            ]);

            $subtotal = 0;
            
            // Crear items de factura y actualizar stock
            foreach ($validated['products'] as $productData) {
                $product = Product::findOrFail($productData['id']);
                $quantity = $productData['quantity'];
                $total = $product->price * $quantity;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'total' => $total,
                ]);
                // Agrega esto para ver qué status se está guardando realmente:
                // Reducir stock
                $product->reduceStock($quantity);
                $subtotal += $total;
            }

            // Calcular totales (15% IGV)
            $tax = $subtotal * 0.15;
            $total = $subtotal + $tax;

            $invoice->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            // Registrar en audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'table_name' => 'invoices',
                'record_id' => $invoice->id,
                'old_values' => null,
                'new_values' => json_encode([
                    'invoice_number' => $invoice->invoice_number,
                    'client_id' => $invoice->client_id,
                    'total' => $invoice->total,
                ]),
            ]);
            

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Factura creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al crear la factura: ' . $e->getMessage()]);
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'user', 'items.product']);
        return view('invoices.show', compact('invoice'));
    }

    public function confirmCancel(CancelInvoiceRequest $request, Invoice $invoice)
    {
        if (!$invoice->canBeCancelledBy(Auth::user())) {
            abort(403, 'No tienes permisos para cancelar esta factura.');
        }

        if ($invoice->isCancelled()) {
            return back()->withErrors(['error' => 'Esta factura ya está cancelada o pagada.']);
        }

        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $oldValues = $invoice->toArray();

            // Restituir stock
            foreach ($invoice->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock', $item->quantity); // Usar increment en lugar de increaseStock
                }
            }

            // Cancelar factura
            $invoice->update([
                'status' => 'cancelado',
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id(),
                'cancellation_reason' => $validated['cancellation_reason'],
            ]);

            // Registrar en audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'cancel',
                'table_name' => 'invoices',
                'record_id' => $invoice->id,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode([
                    'status' => 'cancelled',
                    'cancelled_by' => Auth::id(),
                    'cancellation_reason' => $validated['cancellation_reason'],
                ]),
            ]);

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Factura cancelada exitosamente. El stock ha sido restituido.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al cancelar la factura: ' . $e->getMessage()]);
        }
    }

    public function sendEmail(Invoice $invoice)
    {
        try {
            $invoice->load(['client', 'user', 'items.product']);

            Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));

            // Registrar en audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'send_email',
                'table_name' => 'invoices',
                'record_id' => $invoice->id,
                'old_values' => null,
                'new_values' => json_encode([
                    'sent_to' => $invoice->client->email,
                    'sent_by' => Auth::user()->name,
                    'sent_at' => now(),
                ]),
            ]);

            return back()->with('success', 'Factura enviada exitosamente a ' . $invoice->client->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar el correo: ' . $e->getMessage());
        }
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['client', 'user', 'items.product']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download("factura-{$invoice->invoice_number}.pdf");
    }

    public function destroy(Invoice $invoice)
    {
        try {
            Log::info('Accediendo a destroy (vista de confirmación)', [
                'user' => Auth::id(),
                'invoice' => $invoice->id
            ]);

            // Verificar autorización: solo el creador o un administrador pueden eliminar
            if (!Auth::user()->hasRole('Administrador') && $invoice->user_id !== Auth::id()) {
                Log::warning('Usuario sin autorización en destroy', [
                    'user' => Auth::id(),
                    'invoice' => $invoice->id
                ]);
                abort(403, 'No tienes autorización para eliminar esta factura.');
            }

            // 🚫 Validar que la factura no esté cancelada o pagada
            if (in_array(strtolower($invoice->status), ['cancelado', 'pagado'])) {
                Log::warning('Intento de eliminar factura no permitida por estado', [
                    'user' => Auth::id(),
                    'invoice' => $invoice->id,
                    'status' => $invoice->status
                ]);
                return redirect()
                    ->route('invoices.show', $invoice)
                    ->with('error', 'No se puede eliminar una factura que ya está ' . strtolower($invoice->status) . '.');
            }

            // Cargar relaciones necesarias para la vista
            $invoice->load(['client', 'user', 'items.product']);

            Log::info('Relaciones cargadas, renderizando vista');

            return view('invoices.confirm-delete', compact('invoice'));
        } catch (\Exception $e) {
            Log::error('Error en destroy (vista de confirmación)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'Error al cargar la página de confirmación: ' . $e->getMessage());
        }
    }


    public function confirmDestroy(DeleteInvoiceRequest $request, Invoice $invoice)
    {
        try {
            // Log para debugging
            Log::info('Inicio de confirmDestroy', [
                'user' => Auth::id(),
                'invoice' => $invoice->id,
                'request_data' => $request->validated()
            ]);

            // Verificar autorización
            if (!Auth::user()->hasRole('Administrador') && $invoice->user_id !== Auth::id()) {
                Log::warning('Usuario sin autorización intentó eliminar factura', [
                    'user' => Auth::id(),
                    'invoice' => $invoice->id
                ]);
                abort(403, 'No tienes autorización para eliminar esta factura.');
            }

            $validated = $request->validated();

            Log::info('Validaciones pasadas, iniciando eliminación');

            // Método simple sin transacciones por ahora
            try {
                // Solo restaurar stock si está activa
                if ($invoice->status === 'active') {
                    foreach ($invoice->items as $item) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                    $stockMessage = 'Stock restaurado.';
                } else {
                    $stockMessage = 'No se restauró stock (factura ya cancelada o aprovada).';
                }

                Log::info('Stock procesado');

                // Actualizar campos de eliminación
                $invoice->deletion_reason = $validated['reason'];
                $invoice->deleted_by = Auth::id();
                $invoice->save();

                Log::info('Campos de eliminación actualizados');

                // Soft delete
                $invoice->delete();

                Log::info('Soft delete ejecutado');

                return redirect()
                    ->route('invoices.index')
                    ->with('success', "Factura eliminada exitosamente. {$stockMessage}");
            } catch (\Exception $e) {
                Log::error('Error en el proceso de eliminación', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return back()->with('error', 'Error al eliminar la factura: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Error general en confirmDestroy', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    // Métodos solo para administradores - eliminación completa
    public function eliminados(Request $request)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'Solo los administradores pueden acceder a esta sección.');
        }

        $query = Invoice::onlyTrashed()
            ->with(['client', 'user']);

        // Búsqueda por número, cliente o vendedor
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Cantidad de registros por página
        $perPage = $request->input('per_page', 10);

        $invoices = $query
            ->latest('deleted_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('invoices.eliminados', compact('invoices'));
    }

    
    /**
 * @param RestoreInvoiceRequest $request
 * @param int $id
 * @return \Illuminate\Http\RedirectResponse
 */
public function restore(RestoreInvoiceRequest $request, $id)
{
    $validated = $request->validated();

    $invoice = Invoice::onlyTrashed()->with('items')->findOrFail($id); // <-- Cargar relación items

    DB::beginTransaction();

    try {
        // Verificar que hay suficiente stock para restaurar
        foreach ($invoice->items as $item) {
            $product = Product::find($item->product_id);
            if ($product && $product->stock < $item->quantity) {
                return back()->with('error', "No hay suficiente stock de {$product->name} para restaurar esta factura.");
            }
        }

        // Reducir stock nuevamente
        foreach ($invoice->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->decrement('stock', $item->quantity);
            }
        }

        // Restaurar factura
        $invoice->restore();

        // Registrar en audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'restore_invoice',
            'table_name' => 'invoices',
            'record_id' => $invoice->id,
            'old_values' => ['status' => 'deleted'],
            'new_values' => $invoice->toArray(),
            'details' => [
                'action_description' => "Factura #{$invoice->invoice_number} restaurada",
                'restoration_reason' => $validated['reason'],
                'stock_action' => 'reduced'
            ],
        ]);

        DB::commit();

        return back()->with('success', 'Factura restaurada exitosamente.');
    } catch (\Exception $e) {
        DB::rollback();

        return back()->with('error', 'Error al restaurar la factura: ' . $e->getMessage());
    }
}


    public function forceDelete(ForceDeleteInvoiceRequest $request, $id)
    {
        $validated = $request->validated();

        $invoice = Invoice::onlyTrashed()->findOrFail($id);

        // Registrar en audit log antes de eliminar
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'force_delete_invoice',
            'table_name' => 'invoices',
            'record_id' => $invoice->id,
            'old_values' => $invoice->toArray(),
            'new_values' => null,
            'details' => [
                'action_description' => "Factura #{$invoice->invoice_number} eliminada permanentemente",
                'deletion_reason' => $validated['reason']
            ],
        ]);

        // Eliminar permanentemente
        $invoice->forceDelete();

        return back()->with('success', 'Factura eliminada permanentemente de la base de datos.');
    }

    // Obtener todas las facturas
    public function getInvoices()
    {
        $invoices = Invoice::with(['client', 'user', 'items.product'])->get();
        return response()->json($invoices);
    }

    // Obtener una factura por ID
    public function getInvoiceById($id)
    {
        $invoice = Invoice::with(['client', 'user', 'items.product'])->find($id);
        if (!$invoice) {
            return response()->json(['error' => 'Factura no encontrada'], 404);
        }
        return response()->json($invoice);
    }

    // Crear factura
    // Crear factura vía API (igual que store, pero respuesta JSON)
    public function createInvoice(StoreInvoiceRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // Verificar stock disponible
            foreach ($validated['products'] as $productData) {
                $product = Product::findOrFail($productData['id']);
                if (!$product->hasStock($productData['quantity'])) {
                    return response()->json([
                        'error' => "Stock insuficiente para el producto: {$product->name}. Stock disponible: {$product->stock}"
                    ], 422);
                }
            }

            // Crear factura
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'client_id' => $validated['client_id'],
                'user_id' => Auth::id(),
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
                'status' => 'pendiente',
            ]);

            $subtotal = 0;

            // Crear items de factura y actualizar stock
            foreach ($validated['products'] as $productData) {
                $product = Product::findOrFail($productData['id']);
                $quantity = $productData['quantity'];
                $total = $product->price * $quantity;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'total' => $total,
                ]);
                $product->reduceStock($quantity);
                $subtotal += $total;
            }

            // Calcular totales (15% IGV)
            $tax = $subtotal * 0.15;
            $total = $subtotal + $tax;

            $invoice->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            // Registrar en audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'create_api',
                'table_name' => 'invoices',
                'record_id' => $invoice->id,
                'old_values' => null,
                'new_values' => json_encode([
                    'invoice_number' => $invoice->invoice_number,
                    'client_id' => $invoice->client_id,
                    'total' => $invoice->total,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Factura creada exitosamente.',
                'invoice' => $invoice->load(['client', 'user', 'items.product'])
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Error de validación.',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Actualizar factura vía API
    public function updateInvoice(UpdateInvoiceRequest $request, $id)
    {
        try {
            $invoice = Invoice::find($id);

            if (!$invoice) {
                return response()->json(['error' => 'Factura no encontrada'], 404);
            }

            $validated = $request->validated();

            // Actualizar campos permitidos
            $invoice->update([
                'client_id' => $validated['client_id'] ?? $invoice->client_id,
                'status' => $validated['status'] ?? $invoice->status,
                // Agrega otros campos según sea necesario
            ]);

            // Registrar en audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'update_api',
                'table_name' => 'invoices',
                'record_id' => $invoice->id,
                'old_values' => null,
                'new_values' => json_encode($invoice->toArray()),
            ]);

            return response()->json([
                'message' => 'Factura actualizada exitosamente.',
                'invoice' => $invoice->load(['client', 'user', 'items.product'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación.',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminar (soft delete) factura vía API
    public function deleteInvoice(DeleteInvoiceRequest $request, $id)
    {
        try {
            $invoice = Invoice::with('items')->find($id);

            if (!$invoice) {
                return response()->json(['error' => 'Factura no encontrada'], 404);
            }

            // 🚫 Validar que la factura no esté cancelada o pagada
            if (in_array(strtolower($invoice->status), ['cancelado', 'pagado'])) {
                return response()->json([
                    'error' => 'No se puede eliminar una factura que ya está ' . strtolower($invoice->status) . '.'
                ], 400);
            }

            $validated = $request->validated();

            // Restaurar stock si la factura está activa
            if (strtolower($invoice->status) === 'active') {
                $items = $invoice->items()->get();
                /** @var \App\Models\InvoiceItem $item */
                foreach ($items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            $invoice->deletion_reason = $validated['reason'] ?? null;
            $invoice->deleted_by = Auth::id();
            $invoice->save();

            $invoice->delete();

            // Registrar en audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete_api',
                'table_name' => 'invoices',
                'record_id' => $invoice->id,
                'old_values' => null,
                'new_values' => null,
                'details' => [
                    'deletion_reason' => $validated['reason'] ?? null
                ],
            ]);

            return response()->json([
                'message' => 'Factura eliminada exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    // Restaurar factura eliminada vía API
    public function restoreInvoice(RestoreInvoiceRequest $request, $id)
    {
        try {
            $invoice = Invoice::onlyTrashed()->with('items')->find($id);

            if (!$invoice) {
                return response()->json(['error' => 'Factura no encontrada'], 404);
            }

            $validated = $request->validated();

            // Verificar stock suficiente para restaurar
            $items = $invoice->items()->get();
            /** @var \App\Models\InvoiceItem $item */
            foreach ($items as $item) {
                $product = Product::find($item->product_id);
                if ($product && $product->stock < $item->quantity) {
                    return response()->json([
                        'error' => "No hay suficiente stock de {$product->name} para restaurar esta factura."
                    ], 422);
                }
            }

            // Reducir stock nuevamente
            /** @var \App\Models\InvoiceItem $item */
            foreach ($items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->decrement('stock', $item->quantity);
                }
            }

            $invoice->restore();

            // Registrar en audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'restore_api',
                'table_name' => 'invoices',
                'record_id' => $invoice->id,
                'old_values' => ['status' => 'deleted'],
                'new_values' => $invoice->toArray(),
                'details' => [
                    'restoration_reason' => $validated['reason'] ?? null,
                    'stock_action' => 'reduced'
                ],
            ]);

            return response()->json([
                'message' => 'Factura restaurada exitosamente.',
                'invoice' => $invoice->load(['client', 'user', 'items.product'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminar permanentemente factura vía API
    public function forceDeleteInvoice(DeleteInvoiceRequest $request, $id)
    {
        try {
            $invoice = Invoice::onlyTrashed()->findOrFail($id);

            $validated = $request->validated();

            // Registrar en audit log antes de eliminar
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'force_delete_api',
                'table_name' => 'invoices',
                'record_id' => $invoice->id,
                'old_values' => $invoice->toArray(),
                'new_values' => null,
                'details' => [
                    'deletion_reason' => $validated['reason'] ?? null
                ],
            ]);

            $invoice->forceDelete();

            return response()->json(['message' => 'Factura eliminada permanentemente.']);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
