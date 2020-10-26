<div>
    <div
        class="flex items-center space-x-3"
        @if ($withLoading ?? false)
            wire:loading.class="hidden"
            {{ $loadingAttribute ?? ''}}
        @endif
    >
        @unless ($icon ?? false)
            <x-general.avatar :identifier="$address" />
        @else
            {{ $icon }}
        @endunless

        <div class="flex items-center">
            @if ($prefix ?? false)
                {{ $prefix }}
            @endif

            <a href="{{ route('wallet', $address) }}" class="font-semibold link">
                <x-truncate-middle :value="$address" />
            </a>
        </div>
    </div>

    @if ($withLoading ?? false)
        <x-general.loading-state.recipient-address :address="$address" />
    @endif
</div>
