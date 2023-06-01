@props([
    'model',
    'icon'            => false,
    'prefix'          => false,
    'isListing'       => false,
    'address'         => false,
    'suffix'          => false,
    'withoutReverse'  => false,
    'withoutTruncate' => false,
    'withoutUsername' => false,
    'addressVisible'  => false,
    'withoutReverseClass' => 'space-x-3',
    'containerClass'  => null,
    'contentClass'    => null,
    'withoutLink'     => false,
    'linkClass'       => null,
])

<div @class($containerClass)>
    <div {{ $attributes->class([
        'flex items-center md:flex-row md:justify-start',
        $withoutReverseClass => $withoutReverse,
        'flex-row-reverse md:space-x-4' => ! $withoutReverse,
    ]) }}>
        @unless ($icon)
            <x-general.avatar :identifier="$model->address()" />
        @else
            {{ $icon }}
        @endunless

        <div @class([
            'flex items-center mr-4 md:mr-0',
            $contentClass,
        ])>
            @if ($prefix)
                {{ $prefix }}
            @endif

            @if ($withoutLink)
                <div @class(['font-semibold sm:hidden md:flex', $linkClass])>
            @else
                <a
                    href="{{ route('wallet', $model->address()) }}"
                    @class(['font-semibold sm:hidden md:flex link', $linkClass])
                >
            @endif
                @if ($model->username() && !$withoutUsername)
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
            @if ($withoutLink)
                </div>
            @else
                </a>
            @endif

            @if ($withoutLink)
                <div @class(['hidden font-semibold sm:flex md:hidden', $linkClass])>
            @else
                <a
                    href="{{ route('wallet', $model->address()) }}"
                    @class(['hidden font-semibold sm:flex md:hidden link', $linkClass])
                >
            @endif
                @if ($model->username() && !$withoutUsername)
                    {{ $model->username() }}
                @else
                    {{ $model->address() }}
                @endif
            @if ($withoutLink)
                </div>
            @else
                </a>
            @endif

            @if ($model->username() && !$withoutUsername && $addressVisible)
                <span class="ml-1 truncate">
                    {{ $model->address() }}
                </span>
            @endif

            @if ($suffix)
                {{ $suffix }}
            @endif
        </div>
    </div>
</div>
