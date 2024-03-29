@props([
    'model',
    'prefix'          => false,
    'isListing'       => false,
    'address'         => false,
    'suffix'          => false,
    'withoutTruncate' => false,
    'truncateLength'  => null,
    'withoutUsername' => false,
    'addressVisible'  => false,
    'containerClass'  => null,
    'contentClass'    => null,
    'withoutLink'     => false,
    'linkClass'       => null,
    'delegateNameClass' => null,
])

<div @class($containerClass)>
    <div {{ $attributes->class('flex items-center md:flex-row md:justify-start') }}>
        <div @class([
            'flex items-center md:mr-0',
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
                        <div @class([
                            'delegate-name-truncate-prefix',
                            $delegateNameClass,
                        ])>
                    @elseif ($isListing)
                        <div @class([
                            'delegate-name-truncate-listing',
                            $delegateNameClass,
                        ])>
                    @else
                        <div @class([
                            'delegate-name-truncate',
                            $delegateNameClass,
                        ])>
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
                            <x-truncate-middle :length="$truncateLength">
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
