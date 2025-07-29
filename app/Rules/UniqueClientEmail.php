<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Client;

class UniqueClientEmail implements ValidationRule
{
    protected $ignoreId;
    protected $includeSoftDeleted;

    public function __construct($ignoreId = null, $includeSoftDeleted = true)
    {
        $this->ignoreId = $ignoreId;
        $this->includeSoftDeleted = $includeSoftDeleted;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = Client::where('email', $value);

        // Incluir clientes eliminados si se especifica
        if ($this->includeSoftDeleted) {
            $query->withTrashed();
        }

        // Ignorar un ID específico (útil para updates)
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        $existingClient = $query->first();

        if ($existingClient) {
            if ($existingClient->trashed()) {
                $fail('Este email pertenece a un cliente eliminado. Contacte al administrador para restaurarlo.');
            } else {
                $fail('Ya existe un cliente activo con este email.');
            }
        }
    }
}
