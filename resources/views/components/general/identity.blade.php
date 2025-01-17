@props([
    'model' => null,
    'address' => null,
    'prefix'          => false,
    'isListing'       => false,
    'address'         => false,
    'suffix'          => false,
    'withoutTruncate' => false,
    'truncateLength'  => null,
    'addressVisible'  => false,
    'containerClass'  => null,
    'contentClass'    => null,
    'withoutLink'     => false,
    'linkClass'       => null,
    'validatorNameClass' => null,
])

@php
    if ($model === null && $address === null) {
        throw new Exception('You must provide a model or an address');
    }

    $address = $model ? $model->address() : $address;
@endphp

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
                    href="{{ route('wallet', $address) }}"
                    @class(['font-semibold sm:hidden md:flex link', $linkClass])
                >
            @endif
                @if ($address)
                    {{ $address }}
                @else
                    @if($withoutTruncate)
                        {{ $address }}
                    @else
                        <x-truncate-middle :length="$truncateLength">
                            {{ $address }}
                        </x-truncate-middle>
                    @endisset
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
                    href="{{ route('wallet', $address) }}"
                    @class(['hidden font-semibold sm:flex md:hidden link', $linkClass])
                >
            @endif
                {{ $address }}
            @if ($withoutLink)
                </div>
            @else
                </a>
            @endif

            @if ($suffix)
                {{ $suffix }}
            @endif
        </div>
    </div>
</div>
