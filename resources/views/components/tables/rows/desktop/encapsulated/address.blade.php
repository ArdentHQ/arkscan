@props([
    'model',
    'withoutTruncate' => false,
    'withoutUsername' => false,
    'withoutClipboard' => false,
    'truncateBreakpoint' => 'xl',
    'withoutTransactionCount' => true,
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

<div class="flex flex-col">
    <span {{ $attributes->class('flex justify-between w-full text-sm leading-4.25') }}>
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
            <x-clipboard
                :value="$model->address()"
                :tooltip="trans('pages.wallet.address_copied')"
                class="mr-3"
            />
        @endunless
    </span>

    @unless ($withoutTransactionCount)
        <div class="md-lg:hidden text-xs font-semibold mt-1 leading-4.25">
            <span class="text-theme-secondary-900 dark:text-theme-dark-200">
                {{ $model->transactionCount() }}
            </span>

            <span>@lang('tables.blocks.transactions')</span>
        </div>
    @endunless
</div>
