<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Client;
use App\Models\Invoice;
use App\Http\Controllers\PagosControlador as PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Obtener facturas del cliente autenticado
    Route::get('/invoices', function (Request $request) {
        $client = $request->user();

        return response()->json([
            'invoices' => $client->invoices
        ]);
    });

    // Registrar un nuevo pago
    Route::post('/payments', [PaymentController::class, 'store'])->name('api.payments.store');
});
