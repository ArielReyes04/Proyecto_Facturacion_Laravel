<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

echo "=== Prueba del Dashboard ===\n";

try {
    // Últimas facturas
    $recent_invoices = Invoice::with(['client', 'user'])
        ->whereHas('client') // Solo facturas que tienen cliente
        ->latest()
        ->take(5)
        ->get();
        
    echo "Facturas recientes encontradas: " . $recent_invoices->count() . "\n";
    
    foreach ($recent_invoices as $invoice) {
        echo "Factura: " . $invoice->invoice_number;
        echo " - Cliente: " . ($invoice->client ? $invoice->client->name : 'SIN CLIENTE') . "\n";
    }
    
    // Productos con poco stock
    $low_stock_products = Product::where('stock', '<', 10)
        ->where('is_active', true)
        ->take(5)
        ->get();
        
    echo "\nProductos con poco stock: " . $low_stock_products->count() . "\n";
    
    foreach ($low_stock_products as $product) {
        echo "Producto: " . $product->name . " - Stock: " . $product->stock . "\n";
    }
    
    // Productos más vendidos
    $top_products = DB::table('invoice_items')
        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->join('products', 'invoice_items.product_id', '=', 'products.id')
        ->where('invoices.status', '!=', 'cancelled')
        ->select(
            'products.name',
            DB::raw('SUM(invoice_items.quantity) as total_sold'),
            DB::raw('SUM(invoice_items.total) as total_revenue')
        )
        ->groupBy('products.id', 'products.name')
        ->orderBy('total_sold', 'desc')
        ->take(5)
        ->get();
        
    echo "\nProductos más vendidos: " . $top_products->count() . "\n";
    
    foreach ($top_products as $product) {
        echo "Producto: " . $product->name . " - Vendidos: " . $product->total_sold . "\n";
    }
    
    echo "\n=== Prueba exitosa ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
}
