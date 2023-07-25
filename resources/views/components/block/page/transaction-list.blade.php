@props(['block'])

<x-general.page-section.container
    :title="trans('pages.block.transactions')"
    wrapper-class="flex flex-col flex-1 whitespace-nowrap"
    no-border
>
    <x-tables.desktop.block-transactions :block="$block" />

    <div class="px-3 sm:px-0">
        <x-tables.mobile.block-transactions :block="$block" />
    </div>
</x-general.page-section.container>
