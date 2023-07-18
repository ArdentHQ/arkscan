@props([
    'model',
    'withoutTruncate' => false,
    'withoutUsername' => false,
    'withoutClipboard' => false,
    'truncateBreakpoint' => 'xl',
])

@php
    $truncateHiddenBreakpoint = [
        'sm' => 'sm:hidden',
        'xl' => 'xl:hidden',
    ][$truncateBreakpoint];

    $truncateShowBreakpoint = [
        'sm' => 'hidden sm:inline',
        'xl' => 'hidden xl:inline',
    ][$truncateBreakpoint];
@endphp

<span {{ $attributes->class('flex justify-between w-full text-sm leading-[17px]') }}>
    <span>
        <x-general.identity
            :model="$model"
            :without-truncate="$withoutTruncate"
            :without-username="$withoutUsername"
            without-icon
        >
            <x-slot name="address">
                @unless ($withoutTruncate)
                    <span @class($truncateHiddenBreakpoint)>
                        <x-truncate-middle>{{ $model->address() }}</x-truncate-middle>
                    </span>
                    <span @class($truncateShowBreakpoint)>
                        {{ $model->address() }}
                    </span>
                @else
                    <span class="inline">
                        {{ $model->address() }}
                    </span>
                @endif
            </x-slot>
        </x-general.identity>
    </span>

    @unless ($withoutClipboard)
        <x-ark-clipboard
            :value="$model->address()"
            :tooltip-content="trans('pages.wallet.address_copied')"
            class="mr-3 transition text-theme-primary-400 dark:text-theme-secondary-600 hover:text-theme-primary-700"
            no-styling
        />
    @endunless
</span>
