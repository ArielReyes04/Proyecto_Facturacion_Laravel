<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class PagosControlador extends Controller
{
    /**
     * Display a listing of pending payments.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Pago::with(['invoice', 'payer']);

        // Filtro búsqueda por invoice_id, payer name o transaction_number
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_id', 'like', "%{$search}%")
                ->orWhereHas('payer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })
                ->orWhere('transaction_number', 'like', "%{$search}%");
            });
        }

        // Paginación con límite mínimo 1 y máximo 100
        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(1, min(100, $perPage));

        // Ordenar por actualización más reciente primero
        $payments = $query->orderBy('updated_at', 'desc')->paginate($perPage)->withQueryString();

        // Separar pagos pendientes y pagos procesados para la vista
        $pendingPayments = $payments->filter(fn($p) => $p->status === 'pendiente');
        $processedPayments = $payments->filter(fn($p) => in_array($p->status, ['aprobado', 'rechazado']));

        // Si es petición AJAX (opcional para actualizaciones sin recargar)
        if ($request->ajax() || $request->get('ajax')) {
            $pendingHtml = view('payments.partials.pending-table', ['payments' => $pendingPayments])->render();
            $processedHtml = view('payments.partials.processed-table', ['payments' => $processedPayments])->render();
            $paginationHtml = view('payments.partials.pagination', ['payments' => $payments])->render();

            return response()->json([
                'pending_table' => $pendingHtml,
                'processed_table' => $processedHtml,
                'pagination' => $paginationHtml,
                'total' => $payments->total(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
            ]);
        }

        return view('payments.index', compact('payments', 'pendingPayments', 'processedPayments'));
    }



    /**
     * Store a newly created payment in storage (API).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_type' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'transaction_number' => 'nullable|string|max:100',
            'observations' => 'nullable|string|max:255',
        ]);

        $client = $request->user();

        $invoice = Invoice::where('id', $request->invoice_id)
                          ->where('client_id', $client->id)
                          ->first();

        if (!$invoice) {
            return response()->json([
                'message' => 'La factura no existe o no pertenece al cliente autenticado.'
            ], 403);
        }

        $pago = Pago::create([
            'invoice_id' => $invoice->id,
            'payment_type' => $request->payment_type,
            'amount' => $request->amount,
            'transaction_number' => $request->transaction_number,
            'observations' => $request->observations,
            'status' => 'pendiente',
            'paid_by' => $client->id,
            'validated_by' => null,
            'validated_at' => null,
        ]);

        return response()->json([
            'message' => 'Pago registrado exitosamente.',
            'payment' => $pago
        ], 201);
    }

    /**
     * Aprobar un pago.
     */
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
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);

            $payment->invoice->update(['status' => 'pagado']);

            DB::commit();

            return redirect()->route('payments.index')->with('success', 'Pago aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error aprobando pago: ' . $e->getMessage());

            return redirect()->route('payments.index')->with('error', 'Error al aprobar el pago.');
        }
    }

    public function reject(Request $request, $id)
    {
        $payment = Pago::findOrFail($id);

        // Aquí quitas la validación si quieres probar errores (pero no recomendado)
        // $request->validate([
        //     'rejection_reason' => 'required|string|max:255',
        // ]);

        if ($payment->status !== 'pendiente') {
            return redirect()->route('payments.index')->with('error', 'El pago no está en estado pendiente.');
        }

        DB::beginTransaction();
        try {
            $payment->update([
                'status' => 'rechazado',
                'validated_by' => Auth::id(),
                'validated_at' => now(),
                // Agrega motivo de rechazo a observaciones si te llega en $request
                'observations' => trim(($payment->observations ?? '') . "\nMotivo de rechazo: " . ($request->input('rejection_reason') ?? '')),
            ]);

            // La factura queda en estado pendiente
            $payment->invoice->update(['status' => 'cancelado']);

            DB::commit();

            return redirect()->route('payments.index')->with('success', 'Pago rechazado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rechazando pago: ' . $e->getMessage());

            return redirect()->route('payments.index')->with('error', 'Error al rechazar el pago.');
        }
    }
}
