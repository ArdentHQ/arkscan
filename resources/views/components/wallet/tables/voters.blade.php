@props(['wallet'])

<div
    x-show="tab === 'voters'"
    id="voters-list"
    {{ $attributes->class('w-full') }}
>
    <livewire:wallet-voter-table :wallet="$wallet" />

    <x-webhooks.reload-voters :public-key="$wallet->publicKey()" />
</div>
