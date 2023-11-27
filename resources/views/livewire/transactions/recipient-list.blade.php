<x-general.page-section.container
    :title="trans('pages.transaction.recipients')"
    wrapper-class="flex flex-col flex-1 whitespace-nowrap"
    no-border
>
    <x-skeletons.transaction.recipient-list
        :row-count="$this->perPage"
        :paginator="$recipients"
    >
        <x-tables.desktop.transaction-recipients :recipients="$recipients" />

        <x-tables.mobile.transaction-recipients :recipients="$recipients" />
    </x-skeletons.transaction.recipient-list>

    <x-general.pagination.table :results="$recipients" />
</x-general.page-section.container>
