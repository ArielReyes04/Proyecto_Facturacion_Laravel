<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Client;
use App\Models\Invoice;

Route::get('/clients', function (Request $request) {
    /** @var App\Models\Client $client */
    $client = $request->user(); // cliente autenticado con token

    return $client->invoices; // devuelve solo las facturas del cliente
})->middleware('auth:sanctum');


