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
    'withoutReverseClass' => 'space-x-3',
    'linkWallet'      => true,
])

<div>
    <div @class([
        'flex items-center md:flex-row md:justify-start',
        $withoutReverseClass => $withoutReverse,
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

            @if($linkWallet)
                <a href="{{ route('wallet', $model->address()) }}" class="font-semibold sm:hidden md:flex link">
            @else
                <span class="font-semibold sm:hidden">
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
            @if($linkWallet)
                </a>
            @else
                </span>
            @endif

            @if($linkWallet)
                <a href="{{ route('wallet', $model->address()) }}" class="hidden font-semibold sm:flex md:hidden link">
            @else
                <span class="hidden font-semibold sm:flex md:hidden">
            @endif
                @if ($model->username() && !$withoutUsername)
                    {{ $model->username() }}
                @else
                    {{ $model->address() }}
                @endif
            @if($linkWallet)
                </a>
            @else
                </span>
            @endif

            @if ($suffix)
                {{ $suffix }}
            @endif
        </div>
    </div>
</div>
