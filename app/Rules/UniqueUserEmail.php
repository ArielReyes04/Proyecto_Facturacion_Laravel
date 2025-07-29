<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User;

class UniqueUserEmail implements ValidationRule
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
        $query = User::where('email', $value);

        // Incluir usuarios eliminados si se especifica
        if ($this->includeSoftDeleted) {
            $query->withTrashed();
        }

        // Ignorar un ID específico (útil para updates)
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        $existingUser = $query->first();

        if ($existingUser) {
            if ($existingUser->trashed()) {
                $fail('Este email pertenece a un usuario eliminado. Contacte al administrador para restaurarlo.');
            } else {
                $fail('Este email ya está registrado por otro usuario activo.');
            }
        }
    }
}
