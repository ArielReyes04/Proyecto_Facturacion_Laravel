<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;


class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $query = AuditLog::with('user');

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('action', 'like', "%{$search}%")
                ->orWhere('table_name', 'like', "%{$search}%");
            });
        }

        // Filtro por acción
        if ($request->filled('action_filter') && $request->input('action_filter') !== '') {
            $query->where('action', $request->input('action_filter'));
        }

        $perPage = $request->input('per_page', 20);

        $logs = $query->latest()->paginate($perPage)->withQueryString();

        // Obtener acciones únicas para el filtro
        $actions = AuditLog::distinct()->pluck('action')->sort()->values();

        // Respuesta para AJAX
        if ($request->ajax() || $request->get('ajax')) {
            return response()->json([
                'table' => view('admin.partials.audit-logs-table', compact('logs'))->render(),
                'pagination' => view('admin.partials.audit-logs-pagination', compact('logs'))->render(),
                'total' => $logs->total(),
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage()
            ]);
        }

        return view('admin.audit-logs', compact('logs', 'actions'));
    }

    public function details($id)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $log = AuditLog::with('user')->findOrFail($id);

        // Etiquetas para acciones y tablas
        $actionLabels = [
            'create' => 'Crear',
            'update' => 'Actualizar',
            'delete' => 'Eliminar',
            'restore' => 'Restaurar',
            'status_change' => 'Cambio de Estado',
            'force_delete' => 'Eliminación Permanente',
            'send_email' => 'Envío de Email'
        ];

        $tableLabels = [
            'users' => '👥 Usuarios',
            'clients' => '🏢 Clientes',
            'products' => '📦 Productos',
            'invoices' => '🧾 Facturas',
            'invoice_items' => '📋 Items de Factura',
            'audit_logs' => '📝 Registro de Auditoría',
            'roles' => '🔑 Roles',
            'permissions' => '🛡️ Permisos',
            'model_has_roles' => '👤 Asignación de Roles',
            'categories' => '🏷️ Categorías',
            'suppliers' => '🚚 Proveedores'
        ];

        $data = [
            'id' => $log->id,
            'user_name' => $log->user->name ?? 'Usuario eliminado',
            'user_email' => $log->user->email ?? 'N/A',
            'action' => $log->action,
            'action_label' => $actionLabels[$log->action] ?? ucfirst($log->action),
            'table_name' => $log->table_name,
            'table_label' => $tableLabels[$log->table_name] ?? ('📄 ' . ($log->table_name ?: 'N/A')),
            'record_id' => $log->record_id,
            'old_values' => $log->old_values ?: null,
            'new_values' => $log->new_values ?: null,
            'formatted_date' => $log->created_at->setTimezone('America/Guayaquil')->format('d/m/Y H:i:s'),
            'created_at' => $log->created_at
        ];

        return response()->json($data);
    }
}
