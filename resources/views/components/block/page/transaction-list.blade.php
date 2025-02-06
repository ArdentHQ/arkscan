@props(['block'])

<x-general.page-section.container
    :title="trans('pages.block.transactions')"
    wrapper-class="flex flex-col flex-1 whitespace-nowrap"
    no-bottom-padding
    no-border
>
    <livewire:block-transactions-table :block="$block" />
</x-general.page-section.container>
