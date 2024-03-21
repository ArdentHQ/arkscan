<div
    x-data="{ tab: @entangle('view') }"
    wire:init="triggerViewIsReady"
>
    <x-tabs
        :default="$this->view"
        :options="[
            'validators'     => trans('pages.validators.tabs.validators'),
            'missed-blocks' => trans('pages.validators.tabs.missed_blocks'),
            'recent-votes'  => trans('pages.validators.tabs.recent_votes'),
        ]"
    />

    <div id="validator-table-list">
        <x-validators.tables.validators />

        <x-validators.tables.missed-blocks x-cloak />

        <x-validators.tables.recent-votes x-cloak />

        <x-script.onload-scroll-to-query selector="#validator-table-list" />
    </div>
</div>
