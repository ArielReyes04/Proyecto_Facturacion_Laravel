<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\AuditLog;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Requests\DeleteClientRequest;
use App\Http\Requests\RestoreClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        // Búsqueda por nombre, email o documento
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%");
            });
        }

        // Registros por página
        $perPage = $request->input('per_page', 10);
        $perPage = max(1, min(100, (int) $perPage)); // Limit between 1 and 100

        $clients = $query->latest()->paginate($perPage)->withQueryString();

        // Handle AJAX requests
        if ($request->ajax() || $request->get('ajax')) {
            $tableHtml = view('clients.partials.clients-table', compact('clients'))->render();
            $paginationHtml = view('clients.partials.clients-pagination', compact('clients'))->render();
            
            return response()->json([
                'table' => $tableHtml,
                'pagination' => $paginationHtml,
                'total' => $clients->total(),
                'current_page' => $clients->currentPage(),
                'last_page' => $clients->lastPage()
            ]);
        }

        return view('clients.index', compact('clients'));
    }


    public function create()
    {
        return view('clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        $validated = $request->validated();

        // Crear cliente
        $client = Client::create($validated);

        // Registrar en audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'table_name' => 'clients',
            'record_id' => $client->id,
            'old_values' => null,
            'new_values' => json_encode($validated),
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $validated = $request->validated();
        $oldValues = $client->toArray();

        $client->update($validated);

        // Registrar en audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'table_name' => 'clients',
            'record_id' => $client->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($validated),
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(DeleteClientRequest $request, Client $client)
    {
        $oldValues = $client->toArray();

        $client->delete();

        // Registrar en audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'table_name' => 'clients',
            'record_id' => $client->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode(['razon' => $request->validated()['razon'], 'deleted_by' => Auth::user()->name]),
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    public function restore(RestoreClientRequest $request, $id)
    {
        $client = Client::withTrashed()->findOrFail($id);
        $client->restore();

        // Registrar en audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'restore',
            'table_name' => 'clients',
            'record_id' => $client->id,
            'old_values' => json_encode(['deleted_at' => $client->deleted_at]),
            'new_values' => json_encode(['razon' => $request->validated()['razon'], 'restored_by' => Auth::user()->name]),
        ]);

        return redirect()->route('clients.index')->with('success', 'Cliente restaurado correctamente.');
    }

    public function forceDelete(DeleteClientRequest $request, $id)
    {
        $client = Client::withTrashed()->findOrFail($id);
        $oldValues = $client->toArray();

        // Registrar en audit log antes de eliminar
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'force_delete',
            'table_name' => 'clients',
            'record_id' => $client->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode(['razon' => $request->validated()['razon'], 'force_deleted_by' => Auth::user()->name]),
        ]);

        $client->forceDelete();

        return redirect()->route('clients.index')->with('success', 'Cliente eliminado permanentemente.');
    }

    public function eliminados(Request $request)
    {
        // Solo administradores pueden ver clientes eliminados
        if (!Auth::user()->hasRole('Administrador')) {
            return back()->withErrors(['error' => 'No tienes permisos para realizar esta acción.']);
        }

        $query = Client::onlyTrashed();

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%");
            });
        }

        // Cantidad por página
        $perPage = $request->input('per_page', 10);

        $clientesEliminados = $query
            ->orderBy('deleted_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('clients.eliminados', ['clients' => $clientesEliminados]);
    }

    public function validateField(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $clientId = $request->input('client_id'); // Para ediciones
        
        $errors = [];
        
        switch ($field) {
            case 'name':
                // Validar que solo contenga letras y espacios
                if (empty($value)) {
                    $errors[] = 'El nombre es obligatorio.';
                } elseif (strlen($value) < 2) {
                    $errors[] = 'El nombre debe tener al menos 2 caracteres.';
                } elseif (strlen($value) > 255) {
                    $errors[] = 'El nombre no puede exceder 255 caracteres.';
                } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $value)) {
                    $errors[] = 'El nombre solo puede contener letras y espacios.';
                } else {
                    // Verificar unicidad
                    $query = Client::where('name', $value);
                    if ($clientId) {
                        $query->where('id', '!=', $clientId);
                    }
                    if ($query->exists()) {
                        $errors[] = 'Ya existe un cliente con este nombre.';
                    }
                }
                break;
                
            case 'email':
                if (empty($value)) {
                    $errors[] = 'El correo electrónico es obligatorio.';
                } elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Debe proporcionar un correo electrónico válido.';
                } elseif (strlen($value) > 255) {
                    $errors[] = 'El correo electrónico no puede exceder 255 caracteres.';
                } else {
                    // Verificar unicidad
                    $query = Client::where('email', $value);
                    if ($clientId) {
                        $query->where('id', '!=', $clientId);
                    }
                    if ($query->exists()) {
                        $errors[] = 'Este correo electrónico ya está registrado.';
                    }
                }
                break;
                
            case 'phone':
                if (!empty($value)) {
                    if (strlen($value) > 10) {
                        $errors[] = 'El teléfono no puede exceder 10 dígitos.';
                    } elseif (!preg_match('/^\d{1,10}$/', $value)) {
                        $errors[] = 'El teléfono solo puede contener números (máximo 10 dígitos).';
                    }
                }
                break;
                
            case 'document_number':
                $documentType = $request->input('document_type');
                if (empty($value)) {
                    $errors[] = 'El número de documento es obligatorio.';
                } elseif (strlen($value) > 20) {
                    $errors[] = 'El número de documento no puede exceder 20 caracteres.';
                } else {
                    // Validar formato según tipo de documento
                    switch ($documentType) {
                        case 'CE':
                            if (!preg_match('/^\d{10}$/', $value)) {
                                $errors[] = 'La Cédula debe tener exactamente 10 dígitos.';
                            } elseif (!$this->validateEcuadorianCedula($value)) {
                                $errors[] = 'La Cédula de identidad ecuatoriana no es válida.';
                            }
                            break;
                        case 'RUC':
                            if (!preg_match('/^\d{13}$/', $value)) {
                                $errors[] = 'El RUC debe tener exactamente 13 dígitos.';
                            } elseif (!$this->validateEcuadorianRUC($value)) {
                                $errors[] = 'El RUC ecuatoriano no es válido.';
                            }
                            break;
                        default:
                            $errors[] = 'Debe seleccionar un tipo de documento válido (CE o RUC).';
                    }
                    
                    // Verificar unicidad si no hay errores de formato
                    if (empty($errors)) {
                        $query = Client::where('document_number', $value);
                        if ($clientId) {
                            $query->where('id', '!=', $clientId);
                        }
                        if ($query->exists()) {
                            $errors[] = 'Este número de documento ya está registrado.';
                        }
                    }
                }
                break;
                
            case 'address':
                if (!empty($value) && strlen($value) > 500) {
                    $errors[] = 'La dirección no puede exceder 500 caracteres.';
                }
                break;
        }
        
        return response()->json([
            'valid' => empty($errors),
            'errors' => $errors
        ]);
    }

    /**
     * Validar cédula de identidad ecuatoriana
     */
    private function validateEcuadorianCedula($cedula)
    {
        // Verificar que tenga exactamente 10 dígitos
        if (!preg_match('/^\d{10}$/', $cedula)) {
            return false;
        }

        // Verificar código de provincia (01-24)
        $provincia = intval(substr($cedula, 0, 2));
        if ($provincia < 1 || $provincia > 24) {
            return false;
        }

        // Verificar tercer dígito (debe ser menor que 6 para personas naturales)
        $tercerDigito = intval($cedula[2]);
        if ($tercerDigito >= 6) {
            return false;
        }

        // Validar dígito verificador usando módulo 10
        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $suma = 0;

        for ($i = 0; $i < 9; $i++) {
            $digito = intval($cedula[$i]);
            $producto = $digito * $coeficientes[$i];
            
            if ($producto > 9) {
                $producto -= 9;
            }
            
            $suma += $producto;
        }

        $verificador = (10 - ($suma % 10)) % 10;
        $digitoVerificador = intval($cedula[9]);

        return $verificador === $digitoVerificador;
    }

    /**
     * Validar RUC ecuatoriano
     */
    private function validateEcuadorianRUC($ruc)
    {
        // Verificar que tenga exactamente 13 dígitos
        if (!preg_match('/^\d{13}$/', $ruc)) {
            return false;
        }

        // Verificar código de provincia (01-24)
        $provincia = intval(substr($ruc, 0, 2));
        if ($provincia < 1 || $provincia > 24) {
            return false;
        }

        $tercerDigito = intval($ruc[2]);

        // Persona Natural (3er dígito 0-5)
        if ($tercerDigito >= 0 && $tercerDigito <= 5) {
            // Los primeros 10 dígitos deben ser una cédula válida
            $cedula = substr($ruc, 0, 10);
            if (!$this->validateEcuadorianCedula($cedula)) {
                return false;
            }
            
            // Debe terminar en 001
            return substr($ruc, 10, 3) === '001';
        }
        
        // Sociedad Privada (3er dígito = 9)
        elseif ($tercerDigito === 9) {
            $coeficientes = [4, 3, 2, 7, 6, 5, 4, 3, 2];
            $suma = 0;

            for ($i = 0; $i < 9; $i++) {
                $suma += intval($ruc[$i]) * $coeficientes[$i];
            }

            $verificador = (11 - ($suma % 11)) % 11;
            $digitoVerificador = intval($ruc[9]);

            // Validar dígito verificador y que termine en 001
            return $verificador === $digitoVerificador && substr($ruc, 10, 3) === '001';
        }
        
        // Entidad Pública (3er dígito = 6)
        elseif ($tercerDigito === 6) {
            $coeficientes = [3, 2, 7, 6, 5, 4, 3, 2];
            $suma = 0;

            for ($i = 0; $i < 8; $i++) {
                $suma += intval($ruc[$i]) * $coeficientes[$i];
            }

            $verificador = (11 - ($suma % 11)) % 11;
            $digitoVerificador = intval($ruc[8]);

            // Validar dígito verificador y que termine en 001
            return $verificador === $digitoVerificador && substr($ruc, 10, 3) === '001';
        }

        return false;
    }
}
