<div>
    @if(! count($delegates))
        <div wire:poll="pollDelegates" wire:key="poll_delegates_skeleton">
            <x-tables.desktop.skeleton.delegates.monitor />

            <x-tables.mobile.skeleton.delegates.monitor />
        </div>
    @else
        <div id="network-list" class="w-full" wire:poll.{{ Network::blockTime() }}s="pollDelegates" wire:key="poll_delegates_real">
            <div class="flex overflow-hidden flex-col mb-8 rounded-lg border border-theme-secondary-300 dark:border-theme-secondary-800">
                <div class="p-8 bg-theme-secondary-100 border-theme-secondary-300 dark:border-theme-secondary-800 dark:bg-theme-secondary-900">
                    <div class="grid grid-cols-1 gap-y-8 sm:grid-cols-2 xl:grid-cols-4">
                        <x-general.entity-header-item
                            :title="trans('pages.delegates.statistics.block_count')"
                            :text="$statistics['blockCount']"
                            icon="app-block-id"
                        />
                        <x-general.entity-header-item
                            :title="trans('pages.delegates.statistics.transactions')"
                            icon="app-transaction"
                        >
                            <x-slot name="text">
                                <x-number>
                                    {{ $statistics['transactions'] }}
                                </x-number>
                            </x-slot>
                        </x-general.entity-header-item>
                        <x-general.entity-header-item
                            :title="trans('pages.delegates.statistics.current_delegate')"
                            :text="$statistics['currentDelegate']->username()"
                            :url="route('wallet', $statistics['currentDelegate']->address())"
                            icon="app-current-delegate"
                        />
                        <x-general.entity-header-item
                            :title="trans('pages.delegates.statistics.next_delegate')"
                            :text="$statistics['nextDelegate']->username()"
                            :url="route('wallet', $statistics['nextDelegate']->address())"
                            icon="app-next-delegate"
                            icon-size="md"
                        />
                    </div>
                </div>
            </div>

            <x-tables.desktop.delegates.monitor :delegates="$delegates" :round="$round" />

            <x-tables.mobile.delegates.monitor :delegates="$delegates" />
        </div>
    @endif
</div>
