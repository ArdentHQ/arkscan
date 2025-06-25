@props(['address'])

<x-page-headers.wallet.actions.popup-modal
    :value="$address"
    :title="trans('pages.wallet.legacy-address.title')"
>
    <x-slot name="button">
        <x-ark-icon name="arrows.clock" size="sm" />
    </x-slot>

    <x-slot name="additionalButtons">
        <a
            href="{{ Network::legacyExplorerUrl() }}/addresses/{{ $address }}"
            target="_blank"
            class="p-2 w-full focus-visible:ring-inset button button-secondary button-icon"
        >
            <x-ark-icon
                name="arrows.arrow-external"
                size="sm"
            />
        </a>
    </x-slot>
</x-page-headers.wallet.actions.popup-modal>
