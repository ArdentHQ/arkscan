@props([
    'model',
    'icon'            => false,
    'prefix'          => false,
    'isListing'       => false,
    'address'         => false,
    'suffix'          => false,
    'withoutReverse'  => false,
    'withoutTruncate' => false,
])

<div>
    <div @class([
        'flex items-center md:flex-row md:justify-start',
        'space-x-3' => $withoutReverse,
        'flex-row-reverse md:space-x-4' => ! $withoutReverse,
    ])>
        @unless ($icon)
            <x-general.avatar :identifier="$model->address()" />
        @else
            {{ $icon }}
        @endunless

        <div class="flex items-center mr-4 md:mr-0">
            @if ($prefix)
                {{ $prefix }}
            @endif

            <a href="{{ route('wallet', $model->address()) }}" class="font-semibold sm:hidden md:flex link">
                @if ($model->username())
                    @if ($prefix)
                        <div class="delegate-name-truncate-prefix">
                    @elseif ($isListing)
                        <div class="delegate-name-truncate-listing">
                    @else
                        <div class="delegate-name-truncate">
                    @endif
                        {{ $model->username() }}
                    </div>
                @else
                    @if ($address)
                        {{ $address }}
                    @else
                        @if($withoutTruncate)
                            {{ $model->address() }}
                        @else
                            <x-truncate-middle>
                                {{ $model->address() }}
                            </x-truncate-middle>
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

            @if ($suffix)
                {{ $suffix }}
            @endif
        </div>
    </div>
</div>
