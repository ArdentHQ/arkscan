<div
    x-data="{ tab: @entangle('view') }"
    wire:init="triggerViewIsReady"
>
    <x-tabs
        :default="$this->view"
        :options="[
            'transactions' => trans('pages.home.transactions'),
            'blocks'       => trans('pages.home.blocks'),
        ]"
    />

    <div id="table-list">
        <x-home.tables.transactions />
        <x-home.tables.blocks />

        <x-script.onload-scroll-to-query selector="#table-list" />
    </div>
</div>
