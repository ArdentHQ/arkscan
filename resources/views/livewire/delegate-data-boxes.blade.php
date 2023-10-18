@if(! count($statistics))
    <div class="w-full" wire:poll="pollStatistics" wire:key="poll_statistics_skeleton">
        {{-- <x-delegates.skeletons.data-boxes /> --}}
   </div>
@else
    <div
        id="statistics-list"
        class="grid grid-cols-1 gap-2 w-full sm:grid-cols-2 md:gap-3 xl:grid-cols-4"
        wire:poll.{{ Network::blockTime() }}s="pollStatistics"
        wire:key="poll_statistics_real"
    >
        <x-general.card class="flex items-center space-x-6">
            <x-delegates.monitor.stat
                :title="trans('pages.delegate-monitor.stats.forging')"
                :value="49"
                color="bg-theme-success-700"
            />

            <x-delegates.monitor.stat
                :title="trans('pages.delegate-monitor.stats.missed')"
                :value="0"
                color="bg-theme-warning-700"
            />

            <x-delegates.monitor.stat
                :title="trans('pages.delegate-monitor.stats.not_forging')"
                :value="1"
                color="bg-theme-danger-600"
            />
        </x-general.card>

        <x-general.card>
            <x-general.detail :title="trans('pages.delegate-monitor.stats.current_height')">
                <x-number>{{ $height }}</x-number>
            </x-general.detail>
        </x-general.card>

        <x-general.card>
            <x-general.detail :title="trans('pages.delegate-monitor.stats.current_round')">
                {{ $statistics['blockCount'] }}
            </x-general.detail>
        </x-general.card>

        <x-general.card>
            <x-general.detail :title="trans('pages.delegate-monitor.stats.next_slot')">
                @if ($statistics['nextDelegate'] !== null)
                    <a
                        href="{{ route('wallet', $statistics['nextDelegate']->model()) }}"
                        class="link"
                    >
                        {{ $statistics['nextDelegate']->username() }}
                    </a>
                @else
                    <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                        @lang('general.na')
                    </span>
                @endif
            </x-general.detail>
        </x-general.card>
    </div>
@endif
