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
        <div class="border-l border-theme-secondary-300 dark:border-theme-dark-700 h-[17px] mx-2"></div>

        <div class="text-xs px-1 h-[21px] bg-theme-secondary-200 dark:bg-theme-dark-950 rounded flex space-x-1.5 items-center leading-3.75 text-theme-secondary-700 dark:text-theme-dark-200">
            <x-ark-icon name="transaction.contract" size="xs" />

            <span>@lang('general.contract')</span>
        </div>
    @endif
</span>
