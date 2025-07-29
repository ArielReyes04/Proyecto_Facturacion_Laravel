<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Client;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
{
    $user = Auth::user();
    
    // Estadísticas generales
    $stats = [
        'total_invoices' => Invoice::count(),
        'active_invoices' => Invoice::where('status', '!=', 'cancelled')->count(),
        'cancelled_invoices' => Invoice::where('status', 'cancelled')->count(),
        'total_clients' => Client::count(),
        'active_clients' => Client::where('is_active', true)->count(),
        'total_products' => Product::count(),
        'low_stock_products' => Product::where('stock', '<', 10)->count(),
        'total_revenue' => Invoice::where('status', '!=', 'cancelled')->sum('total'),
    ];

    // Últimas facturas
    $recent_invoices = Invoice::with(['client', 'user'])
        ->whereHas('client')
        ->latest()
        ->take(5)
        ->get();

    // Productos con poco stock
    $low_stock_products = Product::where('stock', '<', 10)
        ->where('is_active', true)
        ->take(5)
        ->get();

    // Ventas mensuales (últimos 6 meses)
    $monthly_sales = Invoice::select(
            DB::raw('EXTRACT(MONTH FROM created_at) as month'),
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('SUM(total) as total')
        )
        ->where('status', '!=', 'cancelled')
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

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

    // Datos específicos para Secretario
    $secretary_data = null;
    if ($user->hasRole('Secretario')) {
        $secretary_data = [
            'new_clients_today' => Client::whereDate('created_at', today())->count(),
            'new_clients_week' => Client::where('created_at', '>=', now()->subWeek())->count(),
            'new_clients_month' => Client::where('created_at', '>=', now()->subMonth())->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
            'recent_clients' => Client::latest()->take(8)->get(),
            'client_growth' => $this->getClientGrowthData(),
            'invoice_status_summary' => $this->getInvoiceStatusSummary(),
            'top_clients' => $this->getTopClients(),
            'client_communication_stats' => $this->getClientCommunicationStats(),
            'upcoming_due_dates' => $this->getUpcomingDueDates(),
            'client_activity' => $this->getClientActivity(),
            'monthly_revenue_trend' => $this->getMonthlyRevenueTrend(),
            'average_invoice_value' => $this->getAverageInvoiceValue(),
            'clients_without_recent_activity' => $this->getClientsWithoutRecentActivity(),
        ];
    }

    // Datos específicos para Bodega
    $warehouse_data = null;
    if ($user->hasRole('Bodega')) {
        $warehouse_data = [
            'critical_stock' => Product::where('stock', '<', 5)->where('is_active', true)->count(),
            'out_of_stock' => Product::where('stock', 0)->where('is_active', true)->count(),
            'total_stock_value' => Product::where('is_active', true)->sum(DB::raw('stock * price')),
            'products_needing_restock' => Product::where('stock', '<', 10)->where('is_active', true)->take(10)->get(),
            'low_stock_products' => Product::where('stock', '>', 0)->where('stock', '<', 10)->where('is_active', true)->get(),
            'recent_stock_movements' => $this->getRecentStockMovements(),
            'stock_by_category' => $this->getStockByCategory(),
            'products_never_sold' => $this->getProductsNeverSold(),
            'inventory_turnover' => $this->getInventoryTurnover(),
            'stock_alerts' => $this->getSimpleStockAlerts(),
            'inventory_summary' => $this->getSimpleInventorySummary(),
            'cost_analysis' => $this->getCostAnalysis(),
            'low_performing_products' => $this->getLowPerformingProducts(), // Agregado
            'seasonal_trends' => $this->getSeasonalTrends(), // Agregado
            'supplier_performance' => $this->getSupplierPerformance(), // Agregado
        ];
    }

    return view('dashboard', compact(
        'stats',
        'recent_invoices',
        'low_stock_products',
        'monthly_sales',
        'top_products',
        'secretary_data',
        'warehouse_data'
    ));
    }

    private function getClientGrowthData()
    {
        return Client::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getInvoiceStatusSummary()
    {
        return Invoice::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
    }

    private function getTopClients()
    {
        return DB::table('invoices')
            ->join('clients', 'invoices.client_id', '=', 'clients.id')
            ->where('invoices.status', '!=', 'cancelled')
            ->select(
                'clients.name',
                'clients.email',
                DB::raw('COUNT(invoices.id) as total_invoices'),
                DB::raw('SUM(invoices.total) as total_spent')
            )
            ->groupBy('clients.id', 'clients.name', 'clients.email')
            ->orderBy('total_spent', 'desc')
            ->take(5)
            ->get();
    }

    private function getRecentStockMovements()
    {
        // Simulamos movimientos de stock basados en ventas recientes
        return DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.created_at', '>=', now()->subDays(7))
            ->select(
                'products.name as product_name',
                'invoice_items.quantity',
                'invoices.created_at as movement_date',
                DB::raw("'Venta' as movement_type")
            )
            ->orderBy('invoices.created_at', 'desc')
            ->take(10)
            ->get();
    }

    private function getStockByCategory()
    {
        // Agrupamos por precio para simular categorías
        return Product::select(
                DB::raw('CASE 
                    WHEN price < 100 THEN \'Económicos\'
                    WHEN price < 500 THEN \'Medio\'
                    ELSE \'Premium\'
                END as category'),
                DB::raw('SUM(stock) as total_stock'),
                DB::raw('COUNT(*) as product_count')
            )
            ->where('is_active', true)
            ->groupBy('category')
            ->get();
    }

    private function getProductsNeverSold()
    {
        return Product::leftJoin('invoice_items', 'products.id', '=', 'invoice_items.product_id')
            ->whereNull('invoice_items.product_id')
            ->where('products.is_active', true)
            ->select('products.*')
            ->take(5)
            ->get();
    }

    private function getInventoryTurnover()
    {
        return DB::table('products')
            ->leftJoin('invoice_items', 'products.id', '=', 'invoice_items.product_id')
            ->select(
                'products.name',
                'products.stock',
                DB::raw('COALESCE(SUM(invoice_items.quantity), 0) as total_sold'),
                DB::raw('CASE 
                    WHEN products.stock > 0 THEN ROUND(COALESCE(SUM(invoice_items.quantity), 0) / products.stock, 2)
                    ELSE 0
                END as turnover_ratio')
            )
            ->where('products.is_active', true)
            ->groupBy('products.id', 'products.name', 'products.stock')
            ->orderBy('turnover_ratio', 'desc')
            ->take(5)
            ->get();
    }

    // Métodos adicionales para Secretario
    private function getClientCommunicationStats()
    {
        return [
            'emails_sent_today' => 0, // Simulado ya que no hay logs específicos
            'emails_sent_week' => 0, // Simulado ya que no hay logs específicos
            'follow_ups_needed' => Invoice::where('status', 'pending')
                ->where('due_date', '<', now()->addDays(3))
                ->count(),
        ];
    }

    private function getUpcomingDueDates()
    {
        return Invoice::with('client')
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date')
            ->take(5)
            ->get();
    }

    private function getClientActivity()
    {
        return Client::whereHas('invoices', function($query) {
                $query->where('created_at', '>=', now()->subMonth());
            })
            ->take(5)
            ->get();
    }

    private function getMonthlyRevenueTrend()
    {
        return Invoice::select(
                DB::raw('TO_CHAR(created_at, \'YYYY-MM\') as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as invoice_count')
            )
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }

    private function getAverageInvoiceValue()
    {
        $current_month = Invoice::where('status', '!=', 'cancelled')
            ->whereRaw('EXTRACT(MONTH FROM created_at) = ?', [now()->month])
            ->avg('total');
        
        $previous_month = Invoice::where('status', '!=', 'cancelled')
            ->whereRaw('EXTRACT(MONTH FROM created_at) = ?', [now()->subMonth()->month])
            ->avg('total');

        return [
            'current_month' => round($current_month ?? 0, 2),
            'previous_month' => round($previous_month ?? 0, 2),
            'percentage_change' => $previous_month > 0 
                ? round((($current_month - $previous_month) / $previous_month) * 100, 2)
                : 0
        ];
    }

    private function getClientsWithoutRecentActivity()
    {
        return Client::whereDoesntHave('invoices', function($query) {
                $query->where('created_at', '>=', now()->subMonths(3));
            })
            ->where('is_active', true)
            ->take(5)
            ->get();
    }

    // Métodos adicionales para Bodega
    private function getLowPerformingProducts()
    {
        return DB::table('products')
            ->leftJoin('invoice_items', 'products.id', '=', 'invoice_items.product_id')
            ->select(
                'products.*',
                DB::raw('COALESCE(SUM(invoice_items.quantity), 0) as total_sold'),
                DB::raw('products.stock * products.price as stock_value')
            )
            ->where('products.is_active', true)
            ->where('products.stock', '>', 0)
            ->groupBy('products.id', 'products.name', 'products.description', 'products.price', 'products.stock', 'products.is_active', 'products.created_at', 'products.updated_at')
            ->orderBy('total_sold', 'asc')
            ->take(5)
            ->get();
    }

    private function getSimpleStockAlerts()
    {
        return [
            'critical_products' => Product::where('stock', '<', 5)
                ->where('is_active', true)
                ->get(),
            'low_stock' => Product::where('stock', '>', 0)
                ->where('stock', '<', 10)
                ->where('is_active', true)
                ->count(),
            'zero_stock' => Product::where('stock', 0)
                ->where('is_active', true)
                ->count(),
            'overstocked' => Product::where('stock', '>', 100)
                ->where('is_active', true)
                ->count(),
            'average_stock_days' => 45, // Valor simulado
        ];
    }

    private function getSimpleInventorySummary()
    {
        return [
            'total_products' => Product::where('is_active', true)->count(),
            'total_stock_units' => Product::where('is_active', true)->sum('stock'),
            'total_stock_value' => Product::where('is_active', true)->sum(DB::raw('stock * price')),
            'categories' => $this->getStockByCategory(),
            'average_product_value' => Product::where('is_active', true)->avg('price'),
        ];
    }

    private function getCostAnalysis()
    {
        return [
            'most_expensive_stock' => Product::where('is_active', true)
                ->orderByRaw('stock * price DESC')
                ->take(3)
                ->get(),
            'least_expensive_stock' => Product::where('is_active', true)
                ->where('stock', '>', 0)
                ->orderByRaw('stock * price ASC')
                ->take(3)
                ->get(),
            'high_value_low_stock' => Product::where('is_active', true)
                ->where('stock', '<', 10)
                ->where('price', '>', 100)
                ->get(),
        ];
    }

    private function getSeasonalTrends()
    {
        // Análisis de ventas por mes para detectar tendencias estacionales
        return DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->select(
                DB::raw('EXTRACT(MONTH FROM invoices.created_at) as month'),
                'products.name',
                DB::raw('SUM(invoice_items.quantity) as total_sold')
            )
            ->where('invoices.created_at', '>=', now()->subYear())
            ->groupBy('month', 'products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->take(10)
            ->get();
    }

    private function getSupplierPerformance()
    {
        // Simulamos análisis de proveedores basado en productos
        return Product::select(
                DB::raw('LEFT(name, 10) as supplier_category'),
                DB::raw('COUNT(*) as product_count'),
                DB::raw('SUM(stock) as total_stock'),
                DB::raw('AVG(price) as avg_price')
            )
            ->where('is_active', true)
            ->groupBy('supplier_category')
            ->orderBy('product_count', 'desc')
            ->take(5)
            ->get();
    }
}
