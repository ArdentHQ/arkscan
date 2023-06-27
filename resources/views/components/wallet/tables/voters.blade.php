@props(['wallet'])

<div
    x-show="tab === 'voters'"
    id="voters-list"
    class="w-full"
>
    <livewire:wallet-voter-table :wallet="$wallet" />
</div>
