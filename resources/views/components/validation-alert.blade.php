@props([
    'type' => 'error',
    'title' => null,
    'message' => null,
    'errors' => null,
    'dismissible' => true
])

@php
    $classes = [
        'error' => 'bg-red-50 border-red-200 text-red-800',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800'
    ];
    
    $icons = [
        'error' => 'fas fa-exclamation-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'success' => 'fas fa-check-circle',
        'info' => 'fas fa-info-circle'
    ];
    
    $iconColors = [
        'error' => 'text-red-400',
        'warning' => 'text-yellow-400',
        'success' => 'text-green-400',
        'info' => 'text-blue-400'
    ];
@endphp

@if($message || $errors)
<div class="mb-4 border rounded-md p-4 {{ $classes[$type] }}" id="validation-alert">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="{{ $icons[$type] }} {{ $iconColors[$type] }}"></i>
        </div>
        <div class="ml-3 flex-1">
            @if($title)
                <h3 class="text-sm font-medium">{{ $title }}</h3>
            @endif
            
            @if($message)
                <div class="{{ $title ? 'mt-2' : '' }} text-sm">
                    {{ $message }}
                </div>
            @endif
            
            @if($errors)
                <div class="{{ ($title || $message) ? 'mt-2' : '' }} text-sm">
                    @if($errors instanceof \Illuminate\Support\MessageBag)
                        @if($errors->any())
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    @elseif(is_array($errors))
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ $errors }}
                    @endif
                </div>
            @endif
        </div>
        
        @if($dismissible)
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" 
                            class="inline-flex p-1.5 rounded-md hover:bg-gray-100 focus:outline-none"
                            onclick="document.getElementById('validation-alert').style.display='none'">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
@endif
