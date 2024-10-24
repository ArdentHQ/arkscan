<div
    x-data="{ tab: @entangle('view').live }"
    wire:init="triggerViewIsReady"
>
    <x-tabs
        :default="$this->view"
        :options="[
            'validators'    => trans('pages.validators.tabs.validators'),
            'missed-blocks' => trans('pages.validators.tabs.missed_blocks'),
            'recent-votes'  => trans('pages.validators.tabs.recent_votes'),
        ]"
    />

    <div id="validator-table-list">
        <div class="hidden sm:block">
            <x-validators.arkconnect.resigned-validator-notice />
            <x-validators.arkconnect.standby-validator-notice />
        </div>

        <x-validators.tables.validators />

        <x-validators.tables.missed-blocks x-cloak />

        <x-validators.tables.recent-votes x-cloak />

        <x-script.onload-scroll-to-query selector="#validator-table-list" />
    </div>
</div>
