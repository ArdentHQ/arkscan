<div>
    @if(! count($delegates))
        <div wire:poll="pollDelegates" wire:key="poll_delegates_skeleton">
            <x-tables.desktop.skeleton.monitor.round />

            <x-tables.mobile.skeleton.monitor.round />
        </div>
    @else
        <div id="network-list" class="w-full" wire:poll.{{ Network::blockTime() }}s="pollDelegates" wire:key="poll_delegates_real">
            <div class="flex flex-col my-8 overflow-hidden border rounded-lg border-theme-secondary-300 dark:border-theme-secondary-800">
                <div class="p-8 bg-theme-secondary-100 border-theme-secondary-300 dark:border-theme-secondary-800 dark:bg-theme-secondary-900">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-4">
                        <x-general.entity-header-item
                            :title="trans('pages.monitor.statistics.block_count')"
                            :text="$statistics['blockCount']"
                            icon="app-block-id"
                        />
                        <x-general.entity-header-item
                            :title="trans('pages.monitor.statistics.transactions')"
                            icon="app-transaction"
                        >
                            <x-slot name="text">
                                <x-number>
                                    {{ $statistics['transactions'] }}
                                </x-number>
                            </x-slot>
                        </x-general.entity-header-item>
                        <x-general.entity-header-item
                            :title="trans('pages.monitor.statistics.current_delegate')"
                            :text="$statistics['currentDelegate']->username()"
                            :url="route('wallet', $statistics['currentDelegate']->address())"
                            icon="app-current-delegate"
                        />
                        <x-general.entity-header-item
                            :title="trans('pages.monitor.statistics.next_delegate')"
                            :text="$statistics['nextDelegate']->username()"
                            :url="route('wallet', $statistics['nextDelegate']->address())"
                            icon="app-next-delegate"
                        />
                    </div>
                </div>
            </div>

            <x-tables.desktop.monitor.round :delegates="$delegates" />

            {{-- <x-tables.mobile.monitor.round :delegates="$delegates" /> --}}
        </div>
    @endif
</div>
