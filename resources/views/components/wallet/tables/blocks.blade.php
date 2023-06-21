@props(['wallet'])

<div
    x-show="tab === 'blocks'"
    id="blocks-list"
    class="w-full"
>
    <livewire:wallet-block-table :wallet="$wallet" />
</div>
