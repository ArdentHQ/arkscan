@props(['publicKey'])

<x-page-headers.wallet.actions.popup-modal
    :value="$publicKey"
    :title="trans('pages.wallet.public_key.title')"
>
    <x-slot name="button">
        <x-ark-icon name="key" size="sm" />
    </x-slot>
</x-page-headers.wallet.actions.popup-modal>
