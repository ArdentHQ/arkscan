<div wire:init="setIsReady" class="mt-4 sm:mt-2">
    <x-general.page-section.container
        :title="trans('pages.transaction.recipients_list')"
        wrapper-class="flex flex-col flex-1 whitespace-nowrap"
        no-border
    >
        <x-skeletons.transaction-recipients
            :row-count="$this->perPage"
            :paginator="$recipients"
        >
            <x-tables.desktop.transaction-recipients :recipients="$recipients" />

            <x-tables.mobile.transaction-recipients :recipients="$recipients" />
        </x-skeletons.transaction-recipients>

        <x-general.pagination.table :results="$recipients" />
    </x-general.page-section.container>
</div>
