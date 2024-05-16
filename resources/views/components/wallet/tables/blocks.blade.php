@props(['wallet'])

<div
    x-show="tab === 'blocks'"
    id="blocks-list"
    {{ $attributes->class('w-full') }}
>
    <livewire:wallet-block-table :wallet="$wallet" />

    <x-webhooks.reload-blocks :public-key="$wallet->publicKey()" />
</div>
