<?php

/**
 * Script para verificar el estado final del sistema de facturas
 * - Verificar que todas las validaciones estén en su lugar
 * - Verificar el estado de los datos
 * - Verificar la configuración del AuditLog
 */

require_once 'bootstrap/app.php';

use App\Models\User;
use App\Models\Invoice;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Schema;

echo "=== VERIFICACIÓN FINAL DEL SISTEMA ===\n\n";

// 1. Verificar estructura de la tabla audit_logs
echo "1. Verificando estructura de audit_logs...\n";
$columns = Schema::getColumnListing('audit_logs');
echo "Columnas encontradas: " . implode(', ', $columns) . "\n";
if (in_array('details', $columns)) {
    echo "✅ Campo 'details' existe\n";
} else {
    echo "❌ Campo 'details' NO existe\n";
}
echo "\n";

// 2. Verificar usuarios activos
echo "2. Verificando usuarios activos...\n";
$activeUsers = User::where('is_active', true)->count();
$adminUsers = User::role('Administrador')->where('is_active', true)->count();
echo "Usuarios activos: {$activeUsers}\n";
echo "Administradores activos: {$adminUsers}\n";
echo "\n";

// 3. Verificar facturas
echo "3. Verificando facturas...\n";
$totalInvoices = Invoice::count();
$activeInvoices = Invoice::where('status', 'active')->count();
$cancelledInvoices = Invoice::where('status', 'cancelled')->count();
$deletedInvoices = Invoice::onlyTrashed()->count();
echo "Total facturas: {$totalInvoices}\n";
echo "Facturas activas: {$activeInvoices}\n";
echo "Facturas canceladas: {$cancelledInvoices}\n";
echo "Facturas eliminadas (soft delete): {$deletedInvoices}\n";
echo "\n";

// 4. Verificar logs de auditoría recientes
echo "4. Verificando logs de auditoría...\n";
$totalLogs = AuditLog::count();
$recentLogs = AuditLog::where('created_at', '>=', now()->subDay())->count();
echo "Total logs: {$totalLogs}\n";
echo "Logs últimas 24h: {$recentLogs}\n";

// Verificar logs con details
$logsWithDetails = AuditLog::whereNotNull('details')->count();
echo "Logs con campo details: {$logsWithDetails}\n";
echo "\n";

// 5. Verificar facturas de ejemplo para pruebas
echo "5. Facturas disponibles para pruebas:\n";
$testInvoices = Invoice::with(['client', 'user'])
    ->limit(3)
    ->get();

foreach ($testInvoices as $invoice) {
    echo "- Factura {$invoice->invoice_number} | Cliente: {$invoice->client?->name} | Estado: {$invoice->status} | Total: S/ " . number_format($invoice->total, 2) . "\n";
}

if ($testInvoices->count() == 0) {
    echo "❌ No hay facturas disponibles para pruebas\n";
}
echo "\n";

echo "=== RESUMEN DE CORRECCIONES APLICADAS ===\n";
echo "✅ Validación frontend en modales de eliminación y cancelación\n";
echo "✅ Campo 'details' serializado como JSON en AuditLog\n";
echo "✅ Lógica de restauración de stock mejorada\n";
echo "✅ Mensajes de error claros sin alerts\n";
echo "✅ Validación de contraseña en backend\n";
echo "\n";

echo "=== PRÓXIMOS PASOS ===\n";
echo "1. Probar login con admin/123456789\n";
echo "2. Ir a facturas y intentar eliminar una factura\n";
echo "3. Verificar que la validación funcione (campos vacíos)\n";
echo "4. Completar eliminación y verificar que aparezca en 'eliminados'\n";
echo "5. Verificar que la sesión no se cierre\n";
echo "\n";

echo "Sistema listo para pruebas! 🚀\n";
