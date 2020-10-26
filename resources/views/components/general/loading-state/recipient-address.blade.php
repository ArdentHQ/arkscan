<div class="flex flex-row items-center space-x-3">
    <div>
        <div wire:loading.class="w-6 h-6 rounded-full md:w-11 md:h-11 loading-state"></div>
    </div>

    <div
        wire:loading.class.remove="hidden"
        class="hidden text-transparent rounded-full loading-state"
    >
        @if ($address ?? false)
            <x-truncate-middle :value="$address" />
        @else
            {{ $text }}
        @endif
    </div>
</div>
