@props(['wallet'])

<div
    x-show="tab === 'transactions'"
    id="transactions-list"
    class="w-full"
>
    <livewire:wallet-transaction-table :wallet="$wallet" />
</div>
