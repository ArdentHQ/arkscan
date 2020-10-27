<div id="network-list" class="w-full" wire:poll.8s>
    <div class="flex flex-col my-8 overflow-hidden border rounded-lg border-theme-secondary-300 dark:border-theme-secondary-800">
        <div class="p-8 bg-theme-secondary-100 border-theme-secondary-300 dark:border-theme-secondary-800 dark:bg-theme-secondary-900">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-4">
                <x-general.entity-header-item
                    :title="trans('pages.monitor.statistics.block_count')"
                    :text="$statistics['blockCount']"
                    icon="app-reward"
                />
                <x-general.entity-header-item
                    :title="trans('pages.monitor.statistics.transactions')"
                    :text="$statistics['transactions']"
                    icon="app-reward"
                />
                <x-general.entity-header-item
                    :title="trans('pages.monitor.statistics.current_delegate')"
                    :text="$statistics['currentDelegate']->username()"
                    :url="route('wallet', $statistics['currentDelegate']->address())"
                    icon="app-reward"
                />
                <x-general.entity-header-item
                    :title="trans('pages.monitor.statistics.next_delegate')"
                    :text="$statistics['nextDelegate']->username()"
                    :url="route('wallet', $statistics['nextDelegate']->address())"
                    icon="app-reward"
                />
            </div>
        </div>
    </div>

    <x-delegates.table-desktop-monitor :delegates="$delegates" />

    {{-- <x-delegates.list-mobile-monitor :delegates="$delegates" /> --}}
</div>
