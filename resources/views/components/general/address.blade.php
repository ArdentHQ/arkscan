<div>
    <div
        class="flex flex-row-reverse items-center md:flex-row md:space-x-3 md:justify-start"
        @if ($withLoading ?? false)
            wire:loading.class="hidden"
            {{ $loadingAttribute ?? ''}}
        @endif
    >
        @unless ($icon ?? false)
            <x-general.avatar :identifier="$model->address()" />
        @else
            {{ $icon }}
        @endunless

        <div class="flex items-center mr-4 md:mr-0">
            @if ($prefix ?? false)
                {{ $prefix }}
            @endif

            <a href="{{ route('wallet', $model->address()) }}" class="font-semibold link sm:hidden md:flex">
                @if ($model->username())
                    {{ $model->username() }}
                @else
                    <x-truncate-middle :value="$model->address()" />
                @endif
            </a>

            <a href="{{ route('wallet', $model->address()) }}" class="hidden font-semibold link sm:flex md:hidden">
                @if ($model->username())
                    {{ $model->username() }}
                @else
                    {{ $model->address() }}
                @endif
            </a>
        </div>
    </div>

    @if ($withLoading ?? false)
        <x-general.loading-state.recipient-address :address="$model->address()" />
    @endif
</div>
