@props(['publicKey'])

<span class="inline-flex items-center">
    <div class="hidden md:inline">
        <x-truncate-middle :length="45">
            {{ $publicKey }}
        </x-truncate-middle>
    </div>

    <div class="hidden sm:block md:hidden">
        <x-truncate-middle :length="20">
            {{ $publicKey }}
        </x-truncate-middle>
    </div>

    <div class="sm:hidden">
        <x-truncate-middle>
            {{ $publicKey }}
        </x-truncate-middle>
    </div>

    <x-clipboard
        :value="$publicKey"
        :tooltip="trans('pages.wallet.bls_public_key_copied')"
    />
</span>
