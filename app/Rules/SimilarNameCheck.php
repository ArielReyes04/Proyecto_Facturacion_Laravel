<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User;
use App\Models\Client;

class SimilarNameCheck implements ValidationRule
{
    protected $model;
    protected $ignoreId;
    protected $threshold;

    public function __construct($model = 'User', $ignoreId = null, $threshold = 85)
    {
        $this->model = $model;
        $this->ignoreId = $ignoreId;
        $this->threshold = $threshold; // Porcentaje de similitud
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $modelClass = $this->model === 'User' ? User::class : Client::class;
        
        $query = $modelClass::select('id', 'name')->withTrashed();

        // Ignorar un ID específico (útil para updates)
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        $existingRecords = $query->get();

        foreach ($existingRecords as $record) {
            $similarity = $this->calculateSimilarity($value, $record->name);
            
            if ($similarity >= $this->threshold) {
                $recordType = $this->model === 'User' ? 'usuario' : 'cliente';
                $fail("Ya existe un {$recordType} con un nombre muy similar: '{$record->name}'. ¿Está seguro de que no es el mismo?");
                break;
            }
        }
    }

    private function calculateSimilarity($string1, $string2): float
    {
        // Normalizar strings (remover acentos, convertir a minúsculas, etc.)
        $string1 = $this->normalizeString($string1);
        $string2 = $this->normalizeString($string2);

        // Calcular similitud usando Levenshtein y similar_text
        $levenshtein = levenshtein($string1, $string2);
        $maxLength = max(strlen($string1), strlen($string2));
        
        if ($maxLength === 0) return 100;
        
        $levenshteinSimilarity = (1 - ($levenshtein / $maxLength)) * 100;

        // También usar similar_text para una segunda opinión
        similar_text($string1, $string2, $similarTextPercentage);

        // Retornar el promedio de ambos métodos
        return ($levenshteinSimilarity + $similarTextPercentage) / 2;
    }

    private function normalizeString($string): string
    {
        // Convertir a minúsculas
        $string = strtolower($string);
        
        // Remover acentos
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        
        // Remover caracteres especiales excepto espacios
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);
        
        // Normalizar espacios múltiples
        $string = preg_replace('/\s+/', ' ', trim($string));
        
        return $string;
    }
}
