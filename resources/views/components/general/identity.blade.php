<div>
    <div class="flex {{ ($withoutReverse ?? false) ? 'space-x-3' : 'flex-row-reverse md:space-x-4' }} items-center md:flex-row md:justify-start">
        @unless ($icon ?? false)
            <x-general.avatar :identifier="$model->address()" />
        @else
            {{ $icon }}
        @endunless

        <div class="flex items-center mr-4 md:mr-0">
            @if ($prefix ?? false)
                {{ $prefix }}
            @endif

            <a href="{{ route('wallet', $model->address()) }}" class="font-semibold sm:hidden md:flex link">
                @if ($model->username())
                    @if ($prefix ?? false)
                        <div class="delegate-name-truncate-prefix">
                    @elseif ($isListing ?? false)
                        <div class="delegate-name-truncate-listing">
                    @else
                        <div class="delegate-name-truncate">
                    @endif
                        {{ $model->username() }}
                    </div>
                @else
                    @if ($address ?? false)
                        {{ $address }}
                    @else
                        @if($withoutTruncate ?? false)
                            {{ $model->address() }}
                        @else
                            @isset($dynamicTruncate)
                            <x-truncate-dynamic>{{ $model->address() }}</x-truncate-dynamic>
                            @else
                            <x-truncate-middle>
                                {{ $model->address() }}
                            </x-truncate-middle>
                            @endif
                        @endisset
                    @endif
                @endif
            </a>

            <a href="{{ route('wallet', $model->address()) }}" class="hidden font-semibold sm:flex md:hidden link">
                @if ($model->username())
                    {{ $model->username() }}
                @else
                    {{ $model->address() }}
                @endif
            </a>

            @if ($suffix ?? false)
                {{ $suffix }}
            @endif
        </div>
    </div>
</div>
