<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\RestoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
class UserController extends Controller 
{
    public function index(Request $request)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 10);
        $perPage = max(1, min(100, (int) $perPage)); // Limit between 1 and 100

        $users = $query->orderBy('name')->paginate($perPage)->withQueryString();

        // Handle AJAX requests
        if ($request->ajax() || $request->has('ajax')) {
            try {
                $tableHtml = view('admin.users.partials.users-table', compact('users'))->render();
                $paginationHtml = view('admin.users.partials.users-pagination', compact('users'))->render();
                
                return response()->json([
                    'table' => $tableHtml,
                    'pagination' => $paginationHtml,
                    'total' => $users->total(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'debug' => [
                        'search' => $request->input('search'),
                        'per_page' => $perPage,
                        'count' => $users->count()
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        }

        return view('admin.users', compact('users'));
    }

    public function create()
    {
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $roles = Role::where('name', '!=', 'Cliente')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);

        // Registrar auditoría
        AuditLog::create([
            'user_id' => Auth::id(),
            'admin_id' => Auth::id(),
            'action' => 'create',
            'table_name' => 'users',
            'record_id' => $user->id,
            'old_values' => null,
            'new_values' => json_encode([
                'name' => $user->name,
                'email' => $user->email,
                'role' => $validated['role'],
                'created_by' => Auth::user()->name
            ]),
        ]);

        return redirect()->route('admin.users')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $roles = Role::all();
        $userRole = $user->roles->first();

        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles->first()->name ?? 'Sin rol',
        ];

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Actualizar rol
        $user->syncRoles([$validated['role']]);

        // Registrar auditoría
        AuditLog::create([
            'user_id' => Auth::id(),
            'admin_id' => Auth::id(),
            'action' => 'update',
            'table_name' => 'users',
            'record_id' => $user->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode([
                'name' => $user->name,
                'email' => $user->email,
                'role' => $validated['role'],
                'updated_by' => Auth::user()->name
            ]),
        ]);

        return redirect()->route('admin.users')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function updateStatus(Request $request, User $user)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        // Si solo se abrió el modal
        if ($request->has('open_modal')) {
            return back()->with([
                'show_modal' => true,
            ])->withInput([
                'user_id' => $user->id,
                'is_active' => $request->is_active,
            ]);
        }

        // Validar usando el Request class
        $statusRequest = new UpdateUserStatusRequest();
        $validated = $request->validate($statusRequest->rules(), $statusRequest->messages());

        // Actualizar estado
        $user->update([
            'is_active' => $validated['is_active'],
            'deactivation_reason' => $validated['reason'],
        ]);

        // Invalidar sesiones
        DB::table('sessions')->where('user_id', $user->id)->delete();

        $user->forceFill([
            'remember_token' => Str::random(60),
        ])->save();

        // Registrar auditoría
        AuditLog::create([
            'user_id' => Auth::id(),
            'admin_id' => Auth::id(),
            'action' => $validated['is_active'] ? 'activate' : 'deactivate',
            'table_name' => 'users',
            'record_id' => $user->id,
            'old_values' => json_encode(['is_active' => !$validated['is_active']]),
            'new_values' => json_encode(['is_active' => $validated['is_active'], 'reason' => $validated['reason']]),
        ]);

        return back()->with('status', 'Estado de usuario actualizado correctamente.');
    }

    public function destroy(DeleteUserRequest $request, User $user)
    {
        $oldValues = $user->toArray();

        $user->delete();

        // Registrar en audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'table_name' => 'users',
            'record_id' => $user->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode(['razon' => $request->validated()['razon'], 'deleted_by' => Auth::user()->name]),
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    public function restore(RestoreUserRequest $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        // Registrar en audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'restore',
            'table_name' => 'users',
            'record_id' => $user->id,
            'old_values' => json_encode(['deleted_at' => $user->deleted_at]),
            'new_values' => json_encode(['razon' => $request->validated()['razon'], 'restored_by' => Auth::user()->name]),
        ]);

        return redirect()->route('admin.users')->with('success', 'Usuario restaurado correctamente.');
    }

    public function forceDelete(RestoreUserRequest $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $oldValues = $user->toArray();

        // Registrar en audit log antes de eliminar
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'force_delete',
            'table_name' => 'users',
            'record_id' => $user->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode(['razon' => $request->validated()['razon'], 'force_deleted_by' => Auth::user()->name]),
        ]);

        $user->forceDelete();

        return redirect()->route('admin.users')->with('success', 'Usuario eliminado permanentemente.');
    }



    public function eliminados(Request $request)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            return back()->withErrors(['error' => 'No tienes permisos para realizar esta acción.']);
        }

        $query = User::onlyTrashed()->with('roles');

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Cantidad de registros por página
        $perPage = $request->input('per_page', 10);

        $usuariosEliminados = $query->orderBy('deleted_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.users.eliminados', ['users' => $usuariosEliminados]);
    }

    public function validateField(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $userId = $request->input('user_id'); // For edit validation
        
        $errors = [];
        $valid = true;
        
        switch ($field) {
            case 'name':
                if (empty($value)) {
                    $errors[] = 'El nombre del usuario es obligatorio.';
                    $valid = false;
                } elseif (strlen($value) < 2) {
                    $errors[] = 'El nombre debe tener al menos 2 caracteres.';
                    $valid = false;
                } elseif (strlen($value) > 255) {
                    $errors[] = 'El nombre no puede exceder 255 caracteres.';
                    $valid = false;
                } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $value)) {
                    $errors[] = 'El nombre solo puede contener letras y espacios.';
                    $valid = false;
                } else {
                    // Check uniqueness
                    $query = User::where('name', $value);
                    if ($userId) {
                        $query->where('id', '!=', $userId);
                    }
                    if ($query->exists()) {
                        $errors[] = 'Ya existe un usuario con este nombre.';
                        $valid = false;
                    }
                }
                break;
                
            case 'email':
                if (empty($value)) {
                    $errors[] = 'El email es obligatorio.';
                    $valid = false;
                } elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'El formato del email no es válido.';
                    $valid = false;
                } else {
                    // Check uniqueness
                    $query = User::where('email', $value);
                    if ($userId) {
                        $query->where('id', '!=', $userId);
                    }
                    if ($query->exists()) {
                        $errors[] = 'Ya existe un usuario con este email.';
                        $valid = false;
                    }
                }
                break;
                
            case 'password':
                if (!empty($value)) { // Solo validar si se proporciona contraseña
                    if (strlen($value) < 8) {
                        $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
                        $valid = false;
                    } elseif (!preg_match('/[A-Z]/', $value)) {
                        $errors[] = 'La contraseña debe contener al menos una letra mayúscula.';
                        $valid = false;
                    } elseif (!preg_match('/[a-z]/', $value)) {
                        $errors[] = 'La contraseña debe contener al menos una letra minúscula.';
                        $valid = false;
                    } elseif (!preg_match('/[0-9]/', $value)) {
                        $errors[] = 'La contraseña debe contener al menos un número.';
                        $valid = false;
                    }
                }
                break;
                
            case 'password_confirmation':
                $password = $request->input('password');
                if (!empty($password) && $value !== $password) {
                    $errors[] = 'Las contraseñas no coinciden.';
                    $valid = false;
                }
                break;
        }
        
        return response()->json([
            'valid' => $valid,
            'errors' => $errors
        ]);
    }

    public function tokens(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Asumiendo que usas Laravel Sanctum para tokens
        $query = $user->tokens();

        // Filtro de búsqueda por nombre del token
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Número de registros por página (default 10)
        $perPage = $request->input('per_page', 10);
        $perPage = max(1, min(100, (int) $perPage)); // límite entre 1 y 100 para seguridad

        $tokens = $query->orderBy('created_at', 'desc')
                        ->paginate($perPage)
                        ->withQueryString(); // conserva filtros en paginación

        // Puedes crear una vista para mostrar los tokens del usuario
        return view('admin.users.tokens', compact('user', 'tokens'));
    }

    public function crearTokenAcceso(Request $request, User $user)
    {
        if (!$user->id) {
            return redirect()->back()->with('error', 'Usuario inválido');
        }

        $request->validate([
            'nombre' => 'required|string|max:255'
        ]);

        $tokenName = $request->input('nombre');

        $token = $user->createToken($tokenName);
        $plainTextToken = $token->plainTextToken;

        return redirect()
            ->route('admin.users.tokens', ['id' => $user->id])
            ->with('token_generado', $plainTextToken);
    }
}
