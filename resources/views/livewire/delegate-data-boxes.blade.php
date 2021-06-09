@if(! count($statistics))
    <div class="w-full" wire:poll="pollStatistics" wire:key="poll_statistics_skeleton">
        <x-delegates.skeletons.data-boxes />
   </div>
@else
    <div id="statistics-list" class="w-full" wire:poll.{{ Network::blockTime() }}s="pollStatistics" wire:key="poll_statistics_real">
        <div class="flex space-x-4 w-full md:flex-col xl:flex-row md:space-x-0 xl:space-x-4 md:space-y-4 xl:space-y-0">
            <div class="flex flex-row py-3 px-6 bg-white rounded-xl dark:bg-theme-secondary-900">
                <div class="flex w-full lg:w-1/2 xl:w-full">
                    <x-general.header-entry
                        :title="trans('pages.delegates.statistics.forging')"
                        :text="$statistics['performances']['forging']"
                        wrapper-class="pr-6 sm:mr-6 lg:mr-0"
                    >
                        <x-slot name="icon">
                            <div class="flex items-center mr-4">
                                <x-delegates.progress-circle
                                    circle-color="success-600"
                                    progress="{{ Percentage::calculate($statistics['performances']['forging'], Network::delegateCount()) }}"
                                >
                                    <x-ark-icon class="rotate-90 text-theme-success-600 border-theme-success-600" name="checkmark-smooth" size="sm" />
                                </x-delegates.progress-circle>
                            </div>
                        </x-slot>
                    </x-general.header-entry>

                    <x-general.header-entry
                        :title="trans('pages.delegates.statistics.missed')"
                        :text="$statistics['performances']['missed']"
                        wrapper-class="pr-6 ml-6 sm:mr-6 lg:mr-0 sm:ml-0 lg:ml-6"
                    >
                        <x-slot name="icon">
                            <div class="flex items-center mr-4">
                                <x-delegates.progress-circle
                                    circle-color="warning-500"
                                    progress="{{ Percentage::calculate($statistics['performances']['missed'], Network::delegateCount()) }}"
                                >
                                    <x-ark-icon class="rotate-90 text-theme-warning-500 border-theme-warning-500" name="pause" size="sm" />
                                </x-delegates.progress-circle>
                            </div>
                        </x-slot>
                    </x-general.header-entry>

                    <x-general.header-entry
                        :title="trans('pages.delegates.statistics.not_forging')"
                        :text="$statistics['performances']['missing']"
                        wrapper-class="ml-6 sm:mr-6 lg:mr-0 lg:pr-6 sm:ml-0 lg:ml-6"
                        without-border
                    >
                        <x-slot name="icon">
                            <div class="flex items-center mr-4">
                                <x-delegates.progress-circle
                                    circle-color="danger-400"
                                    progress="{{ Percentage::calculate($statistics['performances']['missing'], Network::delegateCount()) }}"
                                >
                                    <x-ark-icon class="rotate-90 text-theme-danger-400 border-theme-danger-400" name="cross" size="sm" />
                                </x-delegates.progress-circle>
                            </div>
                        </x-slot>
                    </x-general.header-entry>
                </div>
            </div>

            <div class="flex flex-row space-x-4 w-full">
                <div class="flex flex-grow py-3 px-6 bg-white rounded-xl dark:bg-theme-secondary-900">
                    <x-general.header-entry
                        :title="trans('pages.delegates.statistics.block_count')"
                        :text="$statistics['blockCount']"
                        without-border
                    >
                        <x-slot name="icon">
                            <div class="flex items-center mr-4">
                                <x-delegates.progress-circle circle-color="primary-600" progress="{{ Percentage::calculate((int) $statistics['blockCount'], Network::delegateCount()) }}">
                                    <x-ark-icon class="rotate-90 text-theme-primary-600 border-theme-primary-600" name="app-block-id" />
                                </x-delegates.progress-circle>
                            </div>
                        </x-slot>
                    </x-general.header-entry>
                </div>

                <div class="flex flex-grow py-3 px-6 bg-white rounded-xl dark:bg-theme-secondary-900">
                    @if($statistics['nextDelegate'])
                        <x-general.header-entry
                            :title="trans('pages.delegates.statistics.next_slot')"
                            :text="$statistics['nextDelegate']->username()"
                            :url="route('wallet', $statistics['nextDelegate']->address())"
                            without-border
                        >
                            <x-slot name="icon">
                                <div class="flex items-center md:mr-4">
                                    <div class="hidden rounded-full border-4 border-white md:flex text-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-900">
                                        <div
                                            class="bg-white rounded-full circled-icon dark:bg-theme-secondary-900">
                                            <x-ark-icon name="app-transactions.delegate-registration" />
                                        </div>
                                    </div>
                                </div>
                            </x-slot>
                        </x-general.header-entry>
                    @else
                        <x-delegates.skeletons.data-boxes-next-delegate />
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
