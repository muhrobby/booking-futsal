@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'class' => '',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition duration-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    $variantClasses = match($variant) {
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 focus:ring-blue-500',
        'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 active:bg-gray-400 focus:ring-gray-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800 focus:ring-red-500',
        'success' => 'bg-green-600 text-white hover:bg-green-700 active:bg-green-800 focus:ring-green-500',
        'warning' => 'bg-yellow-600 text-white hover:bg-yellow-700 active:bg-yellow-800 focus:ring-yellow-500',
        'outline' => 'border border-gray-300 text-gray-700 hover:bg-gray-50 active:bg-gray-100 focus:ring-blue-500',
        default => 'bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-800 focus:ring-gray-500',
    };
    
    $sizeClasses = match($size) {
        'sm' => 'text-sm px-3 py-1.5',
        'lg' => 'text-lg px-6 py-3',
        'xl' => 'text-xl px-8 py-4',
        default => 'px-4 py-2 text-base',
    };
    
    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';
    
    $finalClass = trim("{$baseClasses} {$variantClasses} {$sizeClasses} {$disabledClasses} {$class}");
@endphp

<button
    type="{{ $type }}"
    @disabled($disabled)
    {{ $attributes->merge(['class' => $finalClass]) }}
>
    {{ $slot }}
</button>
