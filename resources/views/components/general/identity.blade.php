@props([
    'model',
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
                {{ $model->address() }}
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
