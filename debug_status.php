<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ESTADO DE USUARIOS ===" . PHP_EOL;
$users = App\Models\User::all(['id', 'name', 'email', 'is_active']);
foreach ($users as $user) {
    echo "ID: {$user->id} - {$user->name} - {$user->email} - Activo: " . ($user->is_active ? 'SI' : 'NO') . PHP_EOL;
}

echo PHP_EOL . "=== ESTADO DE FACTURAS ===" . PHP_EOL;
echo "Facturas activas: " . App\Models\Invoice::count() . PHP_EOL;
echo "Facturas eliminadas: " . App\Models\Invoice::onlyTrashed()->count() . PHP_EOL;
echo "Total facturas: " . App\Models\Invoice::withTrashed()->count() . PHP_EOL;

echo PHP_EOL . "=== ÚLTIMOS LOGS DE AUDITORÍA ===" . PHP_EOL;
$logs = App\Models\AuditLog::latest()->take(5)->get(['action', 'table_name', 'details', 'created_at']);
foreach ($logs as $log) {
    $description = is_array($log->details) && isset($log->details['action_description']) 
        ? $log->details['action_description'] 
        : $log->action . ' en ' . $log->table_name;
    echo $log->created_at . " - " . $log->action . " - " . $description . PHP_EOL;
}
