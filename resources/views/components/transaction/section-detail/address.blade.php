@props([
    'address',
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
        :tooltip-content="trans('pages.wallet.address_copied')"
        colors="text-theme-secondary-600 hover:text-theme-secondary-400"
    />
</span>
