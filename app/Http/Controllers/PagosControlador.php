<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Invoice;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Requests\DeletePaymentRequest;
use App\Http\Requests\ForceDeletePaymentRequest;
use App\Http\Requests\RestorePaymentRequest;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
class PagosControlador extends Controller
{
    // Listar pagos
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(1, min(100, $perPage));

        $pendingQuery = Pago::with(['invoice', 'payer'])
            ->where('status', 'pendiente');
        $processedQuery = Pago::with(['invoice', 'payer'])
            ->whereIn('status', ['aprobado', 'rechazado']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $pendingQuery->where(function ($q) use ($search) {
                $q->where('invoice_id', 'like', "%{$search}%")
                  ->orWhereHas('payer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhere('transaction_number', 'like', "%{$search}%");
            });
            $processedQuery->where(function ($q) use ($search) {
                $q->where('invoice_id', 'like', "%{$search}%")
                  ->orWhereHas('payer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhere('transaction_number', 'like', "%{$search}%");
            });
        }

        $pendingPayments = $pendingQuery->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'pending_page');
        $processedPayments = $processedQuery->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'processed_page');

        return view('payments.index', compact('pendingPayments', 'processedPayments'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $invoices = Invoice::where('status', 'pendiente')->get();
        return view('payments.create', compact('invoices'));
    }

    // Guardar pago
    public function store(StorePaymentRequest $request)
    {
        $validated = $request->validated();
        // Buscar la factura
        $invoice = Invoice::findOrFail($validated['invoice_id']);

        // Obtener el id del cliente de la factura
        $clientId = $invoice->client_id;
        $pago = Pago::create([
            'invoice_id' => $validated['invoice_id'],
            'payment_type' => $validated['payment_type'],
            'amount' => $validated['amount'],
            'transaction_number' => $validated['transaction_number'] ?? null,
            'observations' => $validated['observations'] ?? null,
            'status' => 'pendiente',
            'paid_by' => $clientId,
            'validated_by' => null,
            'validated_at' => null,
        ]);

        // Audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'table_name' => 'pagos',
            'record_id' => $pago->id,
            'old_values' => null,
            'new_values' => json_encode($validated),
        ]);

        return redirect()->route('payments.index')->with('success', 'Pago registrado exitosamente.');
    }

    // Mostrar pago
    public function show(Pago $payment)
    {
        return view('payments.show', compact('payment'));
    }

    // Mostrar formulario de edición
    public function edit(Pago $payment)
    {
        $invoices = Invoice::all();
        return view('payments.edit', compact('payment', 'invoices'));
    }

    // Actualizar pago
    public function update(UpdatePaymentRequest $request, Pago $payment)
    {
        $validated = $request->validated();
        $oldValues = $payment->toArray();

        $payment->update($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'table_name' => 'pagos',
            'record_id' => $payment->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($validated),
        ]);

        return redirect()->route('payments.index')->with('success', 'Pago actualizado exitosamente.');
    }

    // Eliminar (soft delete)
    public function destroy(DeletePaymentRequest $request, Pago $payment)
    {
        $oldValues = $payment->toArray();

        $payment->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'table_name' => 'pagos',
            'record_id' => $payment->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode([
                'reason' => $request->input('reason')
            ]),
        ]);

        return redirect()->route('payments.index')->with('success', 'Pago eliminado exitosamente.');
    }

    // Restaurar pago eliminado
    public function restore(RestorePaymentRequest $request, $id)
    {
        $payment = Pago::withTrashed()->findOrFail($id);
        $payment->restore();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'restore',
            'table_name' => 'pagos',
            'record_id' => $payment->id,
            'old_values' => json_encode(['deleted_at' => $payment->deleted_at]),
            'new_values' => json_encode([
                'restored_by' => Auth::user()->name,
                'reason' => $request->input('reason')
            ]),
        ]);

        return redirect()->route('payments.index')->with('success', 'Pago restaurado correctamente.');
    }

    // Eliminar permanentemente
    public function forceDelete(ForceDeletePaymentRequest $request, $id)
    {
        $payment = Pago::withTrashed()->findOrFail($id);
        $oldValues = $payment->toArray();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'force_delete',
            'table_name' => 'pagos',
            'record_id' => $payment->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode([
                'force_deleted_by' => Auth::user()->name,
                'reason' => $request->input('reason')
            ]),
        ]);

        $payment->forceDelete();

        return redirect()->route('payments.index')->with('success', 'Pago eliminado permanentemente.');
    }

    // Aprobar pago
    public function approve(Request $request, $id)
    {
        $payment = Pago::findOrFail($id);

        if ($payment->status !== 'pendiente') {
            return redirect()->route('payments.index')->with('error', 'El pago no está en estado pendiente.');
        }

        DB::beginTransaction();
        try {
            $payment->update([
                'status' => 'aprobado',
                'observations' => trim(($payment->observations ?? '') . "\nObservaciones de aprobación: " . ($request->input('observations') ?? '')),
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);

            $payment->invoice->update(['status' => 'pagado']);
            $payment->invoice->load(['client', 'user', 'items.product']);

            Mail::to($payment->invoice->client->email)->send(new InvoiceMail($payment->invoice));
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'approve',
                'table_name' => 'pagos',
                'record_id' => $payment->id,
                'old_values' => null,
                'new_values' => json_encode(['status' => 'aprobado']),
            ]);

            DB::commit();

            return redirect()->route('payments.index')->with('success', 'Pago aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error aprobando pago: ' . $e->getMessage());

            return redirect()->route('payments.index')->with('error', 'Error al aprobar el pago.');
        }
    }

    // Rechazar pago
    public function reject(Request $request, $id)
    {
        $payment = Pago::findOrFail($id);

        if ($payment->status !== 'pendiente') {
            return redirect()->route('payments.index')->with('error', 'El pago no está en estado pendiente.');
        }

        DB::beginTransaction();
        try {
            $now = now();
            $userId = Auth::id();
            $reason = $request->input('rejection_reason') ?? 'No especificado';

            // Actualizar pago
            $payment->update([
                'status' => 'rechazado',
                'validated_by' => $userId,
                'validated_at' => $now,
                'cancelled_at' => $now,
                'cancelled_by' => $userId,
                'cancellation_reason' => $reason,
                'observations' => trim(($payment->observations ?? '') . "\nMotivo de rechazo: " . $reason),
            ]);
            
            // Actualizar factura relacionada
            $payment->invoice->update([
                'status' => 'cancelado',
                'cancelled_at' => $now,
                'cancelled_by' => $userId,
                'cancellation_reason' => $reason,
            ]);
            $payment->invoice->load(['client', 'user', 'items.product']);

            Mail::to($payment->invoice->client->email)->send(new InvoiceMail($payment->invoice));
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'reject',
                'table_name' => 'pagos',
                'record_id' => $payment->id,
                'old_values' => null,
                'new_values' => json_encode(['status' => 'rechazado']),
            ]);

            DB::commit();

            return redirect()->route('payments.index')->with('success', 'Pago rechazado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rechazando pago: ' . $e->getMessage());

            return redirect()->route('payments.index')->with('error', 'Error al rechazar el pago.');
        }
    }


    // Mostrar pagos eliminados
    public function eliminados()
    {
        $deletedPayments = Pago::onlyTrashed()->with(['invoice', 'payer'])->get();
        return view('payments.eliminados', compact('deletedPayments'));
    }

    // Obtener todos los pagos
    public function getPayments()
    {
        return response()->json(Pago::all());
    }

    // Obtener un pago por ID
    public function getPaymentById($id)
    {
        $payment = Pago::find($id);
        if (!$payment) {
            return response()->json(['error' => 'Pago no encontrado'], 404);
        }
        return response()->json($payment);
    }

    // Crear pago (API)
    public function createPayment(StorePaymentRequest $request)
    {
        $validated = $request->validated();
        $pago = Pago::create([
            'invoice_id' => $validated['invoice_id'],
            'payment_type' => $validated['payment_type'],
            'amount' => $validated['amount'],
            'transaction_number' => $validated['transaction_number'] ?? null,
            'observations' => $validated['observations'] ?? null,
            'status' => 'pendiente',
            'paid_by' => Auth::id() ?? 1, // O ajusta según tu lógica de autenticación API
            'validated_by' => null,
            'validated_at' => null,
        ]);
        return response()->json(['message' => 'Pago creado exitosamente.', 'payment' => $pago], 201);
    }

    // Actualizar pago (API)
    public function updatePayment(UpdatePaymentRequest $request, $id)
    {
        $payment = Pago::find($id);
        if (!$payment) {
            return response()->json(['error' => 'Pago no encontrado'], 404);
        }
        $validated = $request->validated();
        $payment->update($validated);
        return response()->json(['message' => 'Pago actualizado exitosamente.', 'payment' => $payment]);
    }

    // Eliminar (soft delete) pago (API)
    public function deletePayment(DeletePaymentRequest $request, $id)
    {
        $payment = Pago::find($id);
        if (!$payment) {
            return response()->json(['error' => 'Pago no encontrado'], 404);
        }
        $payment->delete();
        return response()->json(['message' => 'Pago eliminado exitosamente.']);
    }

    // Restaurar pago eliminado (API)
    public function restorePayment(RestorePaymentRequest $request, $id)
    {
        $payment = Pago::withTrashed()->find($id);
        if (!$payment) {
            return response()->json(['error' => 'Pago no encontrado'], 404);
        }
        $payment->restore();
        return response()->json(['message' => 'Pago restaurado exitosamente.', 'payment' => $payment]);
    }

    // Eliminar permanentemente pago (API)
    public function forceDeletePayment(ForceDeletePaymentRequest $request, $id)
    {
        $payment = Pago::withTrashed()->find($id);
        if (!$payment) {
            return response()->json(['error' => 'Pago no encontrado'], 404);
        }
        $payment->forceDelete();
        return response()->json(['message' => 'Pago eliminado permanentemente.']);
    }
}
