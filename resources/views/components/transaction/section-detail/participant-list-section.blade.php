@props(['transaction'])

<x-transaction.page-section
    :title="trans('pages.transaction.participants')"
    wrapper-class="flex flex-col flex-1 whitespace-nowrap"
    no-border
>
    <x-tables.desktop.transaction-participants :transaction="$transaction" />

    <x-tables.mobile.transaction-participants :transaction="$transaction" />
</x-transaction.page-section>
