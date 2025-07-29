<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Client;

class UniqueClientDocument implements ValidationRule
{
    protected $ignoreId;
    protected $documentType;
    protected $includeSoftDeleted;

    public function __construct($documentType = null, $ignoreId = null, $includeSoftDeleted = true)
    {
        $this->documentType = $documentType;
        $this->ignoreId = $ignoreId;
        $this->includeSoftDeleted = $includeSoftDeleted;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = Client::where('document_number', $value);

        // Si se proporciona el tipo de documento, también validarlo
        if ($this->documentType) {
            $query->where('document_type', $this->documentType);
        }

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
            $documentTypeText = $this->documentType ?: $existingClient->document_type;
            
            if ($existingClient->trashed()) {
                $fail("Ya existe un cliente eliminado con {$documentTypeText}: {$value}. Contacte al administrador para restaurarlo.");
            } else {
                $fail("Ya existe un cliente activo con {$documentTypeText}: {$value}.");
            }
        }
    }
}
