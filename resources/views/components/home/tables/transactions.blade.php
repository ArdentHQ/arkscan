<div
    x-show="tab === 'transactions'"
    id="transactions-list"
    {{ $attributes->class('w-full') }}
>
    <livewire:home.transactions />
</div>
