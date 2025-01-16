@props([
    'model' => null,
    'withoutTruncate' => false,
    'withoutUsername' => false,
    'withoutClipboard' => false,
    'truncateBreakpoint' => 'xl',
    'withoutTransactionCount' => true,
    'validatorNameClass' => null,
    'address' => null
])

@php
    if ($model === null && $address === null) {
        throw new Exception('You must provide a model or an address');
    }

    $truncateHiddenBreakpoint = [
        'sm' => 'sm:hidden',
        'lg' => 'lg:hidden',
        'xl' => 'xl:hidden',
    ][$truncateBreakpoint];

    $truncateShowBreakpoint = [
        'sm' => 'hidden sm:inline',
        'lg' => 'hidden lg:inline',
        'xl' => 'hidden xl:inline',
    ][$truncateBreakpoint];

    $address = $model ? $model->address() : $address;
@endphp

<div class="flex flex-col">
    <span {{ $attributes->class('flex justify-between w-full text-sm leading-4.25') }}>
        <span>
            <x-general.identity
                :model="$model"
                :address="$address"
                :without-truncate="$withoutTruncate"
                :without-username="$withoutUsername"
                :validator-name-class="$validatorNameClass"
            >
                <x-slot name="address">
                    @unless ($withoutTruncate)
                        <span @class($truncateHiddenBreakpoint)>
                            <x-truncate-middle>{{ $address }}</x-truncate-middle>
                        </span>
                        <span @class($truncateShowBreakpoint)>
                            {{ $address }}
                        </span>
                    @else
                        <span class="inline">
                            {{ $address }}
                        </span>
                    @endif
                </x-slot>
            </x-general.identity>
        </span>

        @unless ($withoutClipboard)
            <x-clipboard
                :value="$address"
                :tooltip="trans('pages.wallet.address_copied')"
                class="mr-3"
            />
        @endunless
    </span>

    @unless ($withoutTransactionCount)
        <div class="mt-1 text-xs font-semibold leading-4.25 md-lg:hidden">
            <span class="text-theme-secondary-900 dark:text-theme-dark-200">
                {{ $model->transactionCount() }}
            </span>

            <span>@lang('tables.blocks.transactions')</span>
        </div>
    @endunless
</div>
