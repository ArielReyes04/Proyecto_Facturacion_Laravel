<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Client;
use App\Models\Invoice;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PagosControlador ;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Administrador y Secretario
    Route::middleware('role:Administrador|Secretario')->group(function () {
        Route::get('/getClient', [ClientController::class, 'getClient']);
        Route::get('/getClient/{id}', [ClientController::class, 'getClientId']);
        Route::post('/createClient', [ClientController::class, 'createClient']);
        Route::put('/updateClient/{id}', [ClientController::class, 'updateClient']);
    });

    // Solo Administrador
    Route::middleware('role:Administrador')->group(function () {
        Route::delete('/deleteClient/{id}', [ClientController::class, 'deleteClient'])->name('api.clients.delete');
        Route::post('/restoreClient/{id}', [ClientController::class, 'restoreClient'])->name('api.clients.restore');
        Route::delete('/forceDeleteClient/{id}', [ClientController::class, 'forceDeleteClient'])->name('api.clients.forceDelete');
    });

    Route::middleware('role:Administrador|Bodega')->group(function () {
        Route::get('/getProducts', [ProductController::class, 'getProducts']);
        Route::get('/getProduct/{id}', [ProductController::class, 'getProductById']);
        Route::post('/createProduct', [ProductController::class, 'createProduct']);
        Route::put('/updateProduct/{id}', [ProductController::class, 'updateProduct']);
    });

    Route::middleware('role:Administrador')->group(function () {
        Route::delete('/deleteProduct/{id}', [ProductController::class, 'deleteProduct']);
        Route::post('/restoreProduct/{id}', [ProductController::class, 'restoreProduct']);
        Route::delete('/forceDeleteProduct/{id}', [ProductController::class, 'forceDeleteProduct']);
    });

    Route::middleware('role:Administrador|Ventas')->group(function () {
        Route::get('/getInvoices', [\App\Http\Controllers\InvoiceController::class, 'getInvoices']);
        Route::get('/getInvoice/{id}', [\App\Http\Controllers\InvoiceController::class, 'getInvoiceById']);
        Route::post('/createInvoice', [\App\Http\Controllers\InvoiceController::class, 'createInvoice']);
        Route::put('/updateInvoice/{id}', [\App\Http\Controllers\InvoiceController::class, 'updateInvoice']);
    });

    Route::middleware('role:Administrador')->group(function () {
        Route::delete('/deleteInvoice/{id}', [\App\Http\Controllers\InvoiceController::class, 'deleteInvoice']);
        Route::post('/restoreInvoice/{id}', [\App\Http\Controllers\InvoiceController::class, 'restoreInvoice']);
        Route::delete('/forceDeleteInvoice/{id}', [\App\Http\Controllers\InvoiceController::class, 'forceDeleteInvoice']);
    });

    Route::middleware('role:Administrador|Pagos')->group(function () {
        Route::get('/payments', [PagosControlador::class, 'getPayments']);
        Route::get('/payments/{id}', [PagosControlador::class, 'getPaymentById']);
        Route::post('/createPayments', [PagosControlador::class, 'createPayment']);
        Route::put('/updatePayments/{id}', [PagosControlador::class, 'updatePayment']);
    });

    Route::middleware('role:Administrador')->group(function () {
        Route::delete('/deletePayments/{id}', [PagosControlador::class, 'deletePayment']);
        Route::post('/restorePayments/{id}', [PagosControlador::class, 'restorePayment']);
        Route::delete('/forcedeletePayments/{id}', [PagosControlador::class, 'forceDeletePayment']);
    });

    Route::middleware('role:Cliente')->group(function () {
        Route::get('/invoices/client', action: function (Request $request) {
            $client = $request->user();
            return response()->json([
                'invoices' => $client->invoices
            ]);
        });
        Route::post('/createPaymentsClient', [PagosControlador::class, 'createPayment']);
    });

    

});
