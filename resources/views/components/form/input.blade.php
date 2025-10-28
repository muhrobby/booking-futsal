<!-- Text Input Component -->
<div class="mb-4">
    @isset($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required ?? false)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endisset

    <input 
        type="{{ $type ?? 'text' }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value ?? '') }}"
        placeholder="{{ $placeholder ?? '' }}"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition {{ $class ?? '' }} 
        @error($name) border-red-500 focus:ring-red-500 @enderror"
        @if($required ?? false) required @endif
        @isset($readonly){{ 'readonly' }}@endisset
        @isset($disabled){{ 'disabled' }}@endisset
        @isset($autocomplete){{ 'autocomplete=' . $autocomplete }}@endisset
        @isset($attributes)
            @foreach($attributes as $attr => $val)
                {{ $attr }}="{{ $val }}"
            @endforeach
        @endisset
    />

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @isset($hint)
        <p class="mt-1 text-sm text-gray-500">{{ $hint }}</p>
    @endisset
</div>
