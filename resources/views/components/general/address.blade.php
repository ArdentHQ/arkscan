<div class="flex items-center space-x-3">
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
