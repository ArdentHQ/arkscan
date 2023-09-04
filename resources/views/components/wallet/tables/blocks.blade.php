@props(['wallet'])

<div
    x-show="tab === 'blocks'"
    id="blocks-list"
    {{ $attributes->class('w-full') }}
>
    <livewire:wallet-block-table :wallet="$wallet" />
</div>
