@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'help' => null,
    'icon' => null,
    'options' => [], // Para select
    'rows' => 4, // Para textarea
    'validation' => [], // Reglas de validación personalizadas
])

@php
    $fieldId = $name;
    $hasError = $errors->has($name);
    $errorMessage = $hasError ? $errors->first($name) : null;
    $oldValue = old($name, $value);
@endphp

<div class="mb-4">
    <!-- Label -->
    <label for="{{ $fieldId }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <!-- Campo de entrada -->
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="{{ $icon }} text-gray-400"></i>
            </div>
        @endif
        
        @if($type === 'select')
            <select 
                id="{{ $fieldId }}"
                name="{{ $name }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                class="mt-1 block w-full {{ $icon ? 'pl-10' : '' }} rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $hasError ? 'border-red-500 focus:border-red-500' : '' }}"
            >
                <option value="">Seleccione una opción</option>
                @foreach($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" {{ $oldValue == $optionValue ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
            
        @elseif($type === 'textarea')
            <textarea
                id="{{ $fieldId }}"
                name="{{ $name }}"
                rows="{{ $rows }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                class="mt-1 block w-full {{ $icon ? 'pl-10' : '' }} rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $hasError ? 'border-red-500 focus:border-red-500' : '' }}"
            >{{ $oldValue }}</textarea>
            
        @else
            <input
                type="{{ $type }}"
                id="{{ $fieldId }}"
                name="{{ $name }}"
                value="{{ $oldValue }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                class="mt-1 block w-full {{ $icon ? 'pl-10' : '' }} rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $hasError ? 'border-red-500 focus:border-red-500' : '' }}"
                @if(in_array('email', $validation))
                    data-validation="email"
                @endif
                @if(in_array('document', $validation))
                    data-validation="document"
                @endif
                @if(in_array('password', $validation))
                    data-validation="password"
                @endif
            >
        @endif
        
        <!-- Indicador de fortaleza de contraseña -->
        @if($type === 'password' && $name === 'password')
            <div id="password-strength" class="mt-1 text-sm text-gray-500"></div>
        @endif
    </div>
    
    <!-- Mensaje de ayuda -->
    @if($help)
        <p class="mt-1 text-sm text-gray-500">{{ $help }}</p>
    @endif
    
    <!-- Mensaje de error del servidor -->
    @if($hasError)
        <p class="mt-1 text-sm text-red-600">{{ $errorMessage }}</p>
    @endif
    
    <!-- Contenedor para errores de validación en tiempo real -->
    <div id="{{ $fieldId }}-validation-errors" class="mt-1"></div>
</div>

@if($type === 'password' && $name === 'password')
    @once
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const passwordField = document.getElementById('{{ $fieldId }}');
                if (passwordField) {
                    // Agregar tooltip con requisitos
                    const tooltip = document.createElement('div');
                    tooltip.className = 'absolute z-10 invisible opacity-0 transition-opacity bg-gray-800 text-white text-xs rounded py-1 px-2 mt-1';
                    tooltip.style.top = '100%';
                    tooltip.style.left = '0';
                    tooltip.innerHTML = 'Debe contener: 8+ caracteres, 1 mayúscula, 1 minúscula, 1 número, 1 símbolo';
                    
                    passwordField.parentNode.style.position = 'relative';
                    passwordField.parentNode.appendChild(tooltip);
                    
                    passwordField.addEventListener('focus', () => {
                        tooltip.classList.remove('invisible', 'opacity-0');
                    });
                    
                    passwordField.addEventListener('blur', () => {
                        tooltip.classList.add('invisible', 'opacity-0');
                    });
                }
            });
        </script>
        @endpush
    @endonce
@endif
