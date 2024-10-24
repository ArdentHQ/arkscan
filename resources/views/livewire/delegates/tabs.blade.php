<div
    x-data="{ tab: @entangle('view').live }"
    wire:init="triggerViewIsReady"
>
    <x-tabs
        :default="$this->view"
        :options="[
            'delegates'     => trans('pages.delegates.tabs.delegates'),
            'missed-blocks' => trans('pages.delegates.tabs.missed_blocks'),
            'recent-votes'  => trans('pages.delegates.tabs.recent_votes'),
        ]"
    />

    <div id="delegate-table-list">
        <div class="hidden sm:block">
            <x-delegates.arkconnect.resigned-delegate-notice />
            <x-delegates.arkconnect.standby-delegate-notice />
        </div>

        <x-delegates.tables.delegates />

        <x-delegates.tables.missed-blocks x-cloak />

        <x-delegates.tables.recent-votes x-cloak />

        <x-script.onload-scroll-to-query selector="#delegate-table-list" />
    </div>
</div>
