@props(['transaction'])

<x-transaction.page-section
    id="recipients-list"
    :title="trans('pages.transaction.recipients_list')"
    wrapper-class="flex flex-col flex-1 whitespace-nowrap"
    no-border
>
    <x-tables.desktop.transaction-recipients :transaction="$transaction" />

    <x-tables.mobile.transaction-recipients :transaction="$transaction" />
</x-transaction.page-section>
