<!-- Stats Card Component -->
<div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $value }}</p>
            
            @isset($subtitle)
                <p class="mt-2 text-sm text-gray-500">{{ $subtitle }}</p>
            @endisset
        </div>

        @isset($icon)
            <div class="rounded-lg bg-blue-100 p-3 text-blue-600">
                {!! $icon !!}
            </div>
        @elseif ($slot->isNotEmpty())
            <div class="rounded-lg bg-blue-100 p-3 text-blue-600">
                {{ $slot }}
            </div>
        @endisset
    </div>

    @isset($trend)
        <div class="mt-4 flex items-center">
            @if($trend['direction'] === 'up')
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 7H12z" clip-rule="evenodd" />
                </svg>
                <span class="ml-2 text-sm font-medium text-green-600">{{ $trend['value'] }}</span>
            @elseif($trend['direction'] === 'down')
                <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12 13a1 1 0 110 2H7a1 1 0 01-1-1V9a1 1 0 112 0v3.586l4.293-4.293a1 1 0 011.414 1.414L9.414 13H12z" clip-rule="evenodd" />
                </svg>
                <span class="ml-2 text-sm font-medium text-red-600">{{ $trend['value'] }}</span>
            @else
                <span class="text-sm font-medium text-gray-600">{{ $trend['value'] }}</span>
            @endif
            <span class="ml-2 text-sm text-gray-500">vs bulan lalu</span>
        </div>
    @endisset

    @isset($action)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <a href="{{ $action['url'] }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                {{ $action['label'] }} →
            </a>
        </div>
    @endisset
</div>
