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

        @if ($this->hasLoadedView('missed-blocks'))
            <x-validators.tables.missed-blocks
                :defer-loading="false"
                x-cloak
            />
        @else
            <div x-show="tab === 'missed-blocks'">
                <x-skeletons.validators.missed-blocks
                    :paginator="new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1)"
                    :is-ready="false"
                    x-cloak
                />
            </div>
        @endif

        {{-- @if ($this->hasLoadedView('recent-votes'))
            <x-validators.tables.recent-votes
                :defer-loading="false"
                x-cloak
            />
        @else
            <div x-show="tab === 'recent-votes'">
                <x-skeletons.validators.recent-votes
                    :paginator="new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1)"
                    :is-ready="false"
                    x-cloak
                />
            </div>
        @endif --}}

        <x-script.onload-scroll-to-query selector="#validator-table-list" />
    </div>
</div>
