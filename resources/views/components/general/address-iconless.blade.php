<div class="flex items-center justify-between space-x-3" {{ $loadingAttribute ?? ''}} wire:loading.class="hidden">
    <div class="flex items-center">
        @if ($prefix ?? false)
            {{ $prefix }}
        @endif

        <a href="{{ route('wallet', $address) }}" class="font-semibold link">
            <x-truncate-middle :value="$address" />
        </a>
    </div>
</div>
