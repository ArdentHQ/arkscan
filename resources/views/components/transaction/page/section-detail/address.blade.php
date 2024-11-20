@props([
    'address',
    'isContract' => false,
])

<span class="inline-flex items-center">
    <a
        href="{{ route('wallet', $address) }}"
        class="link"
    >
        <div class="hidden md:inline">
            {{ $address }}
        </div>

        <div class="md:hidden">
            <x-truncate-middle>
                {{ $address }}
            </x-truncate-middle>
        </div>
    </a>

    <x-clipboard
        :value="$address"
        :tooltip="trans('pages.wallet.address_copied')"
    />

    @if ($isContract)
        <div class="mx-2 border-l border-theme-secondary-300 h-[17px] dark:border-theme-dark-700"></div>

        <div class="flex items-center px-1 space-x-1.5 text-xs rounded h-[21px] bg-theme-secondary-200 leading-3.75 text-theme-secondary-700 dark:bg-theme-dark-950 dark:text-theme-dark-200">
            <x-ark-icon name="transaction.contract" size="xs" />

            <span>@lang('general.contract')</span>
        </div>
    @endif
</span>
