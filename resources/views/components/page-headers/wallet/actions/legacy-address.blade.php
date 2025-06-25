@props(['address'])

<x-page-headers.wallet.actions.popup-modal
    :value="$address"
    :title="trans('pages.wallet.legacy-address.title')"
>
    <x-slot name="button">
        <x-ark-icon name="arrows.clock" size="sm" />
    </x-slot>
</x-page-headers.wallet.actions.popup-modal>
