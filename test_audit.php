<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Configurar conexión (usando los mismos parámetros de la app)
$db = new DB;
$db->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'prueba_p2',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$db->setAsGlobal();
$db->bootEloquent();

try {
    // Probar insertar un registro de AuditLog con details
    $result = DB::table('audit_logs')->insert([
        'user_id' => 1,
        'action' => 'test_action',
        'table_name' => 'test_table',
        'record_id' => 999,
        'old_values' => json_encode(['test' => 'old']),
        'new_values' => json_encode(['test' => 'new']),
        'details' => json_encode([
            'action_description' => 'Test audit log entry',
            'test_field' => 'test_value'
        ]),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    
    if ($result) {
        echo "✅ AuditLog test insertado correctamente\n";
        
        // Verificar que se puede leer
        $record = DB::table('audit_logs')->where('action', 'test_action')->first();
        if ($record) {
            echo "✅ AuditLog test leído correctamente\n";
            echo "Details: " . $record->details . "\n";
            
            // Limpiar
            DB::table('audit_logs')->where('action', 'test_action')->delete();
            echo "✅ AuditLog test limpiado\n";
        }
    } else {
        echo "❌ Error al insertar AuditLog test\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
