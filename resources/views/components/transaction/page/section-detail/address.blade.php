@props([
    'address',
    'isContract' => false,
])

<div class="flex items-center">
    <a
        href="{{ route('wallet', $address) }}"
        class="min-w-0 link"
    >
        <div class="hidden md:inline">
            <x-truncate-dynamic>{{ $address }}</x-truncate-dynamic>
        </div>

        <div class="md:hidden">
            @unless ($isContract)
                <x-truncate-middle>
                    {{ $address }}
                </x-truncate-middle>
            @else
                @lang('general.contract')
            @endif
        </div>
    </a>

    @if ($isContract)
        <div class="ml-3 w-5 h-5 md:hidden ark-info-element">
            <x-ark-info
                :tooltip="$address"
                type="info"
            />
        </div>
    @endif

    <x-clipboard
        :value="$address"
        :tooltip="trans('pages.wallet.address_copied')"
    />

    @if ($isContract)
        <div class="hidden items-center md:flex">
            <div class="mx-2 border-l border-theme-secondary-300 h-[17px] dark:border-theme-dark-700"></div>

            <div class="flex items-center px-1 space-x-1.5 text-xs rounded h-[21px] bg-theme-secondary-200 leading-3.75 text-theme-secondary-700 dark:bg-theme-dark-950 dark:text-theme-dark-200">
                <x-ark-icon name="transaction.contract" size="xs" />

                <span>@lang('general.contract')</span>
            </div>
        </div>
    @endif
</div>
