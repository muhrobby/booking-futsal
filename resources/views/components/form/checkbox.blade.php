<!-- Checkbox Component -->
<div class="mb-4">
    <div class="flex items-center">
        <input 
            type="checkbox"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $value ?? '1' }}"
            class="w-4 h-4 border border-gray-300 rounded text-blue-600 focus:ring-2 focus:ring-blue-500 cursor-pointer transition {{ $class ?? '' }}
            @error($name) border-red-500 @enderror"
            @if(old($name, $checked ?? false)) checked @endif
            @if($required ?? false) required @endif
            @isset($disabled){{ 'disabled' }}@endisset
        />
        
        @isset($label)
            <label for="{{ $name }}" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                {{ $label }}
                @if($required ?? false)
                    <span class="text-red-600">*</span>
                @endif
            </label>
        @endisset
    </div>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @isset($hint)
        <p class="mt-1 text-sm text-gray-500">{{ $hint }}</p>
    @endisset
</div>
