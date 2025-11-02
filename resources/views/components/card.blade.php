@props([
    'header' => null,
    'footer' => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => "bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition {$class}"]) }}>
    @if ($header)
        <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900">{{ $header }}</h3>
        </div>
    @endif

    <div class="px-3 sm:px-6 py-3 sm:py-4">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200 bg-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>
