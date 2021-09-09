<div class="pb-8 -mt-2 w-full bg-white content-container dark:bg-theme-secondary-900">
    <div class="flex py-4 px-8 w-full rounded-xl border border-theme-secondary-300 dark:border-theme-secondary-800">
        <div class="flex flex-col justify-between space-y-6 w-full sm:flex-row sm:space-y-0">
            <div class="flex justify-start">
                <div class="flex md:hidden">
                    <x-general.entity-header-item
                        :title="trans('pages.wallet.voting_for')"
                        without-icon
                        :text="$vote->username()"
                        :url="route('wallet', $vote->address())"
                        content-class="space-y-2"
                        wrapper-class="border-none"
                    />
                </div>

                <div class="hidden md:flex">
                    <x-general.entity-header-item
                        :title="trans('pages.wallet.voting_for')"
                        icon="app-transactions.vote"
                        :text="$vote->username()"
                        :url="route('wallet', $vote->address())"
                        wrapper-class="border-none"
                    />
                </div>
            </div>

            <div class="flex space-x-8 sm:space-x-4">
                @if(! $vote->isResigned())
                    <x-general.entity-header-item
                        :title="trans('pages.wallet.rank')"
                        without-icon
                        content-class="pr-7 space-y-2 border-r sm:pr-0 sm:text-right sm:border-r-0 border-theme-secondary-300 dark:border-theme-secondary-800"
                    >
                        <x-slot name="text">
                            @if ($vote->isResigned())
                                <x-details.resigned />
                            @else
                                @lang('pages.wallet.vote_rank', [$vote->rank()])
                            @endif
                        </x-slot>
                    </x-general.entity-header-item>
                @endif

                <x-general.entity-header-item
                    :title="trans('pages.wallet.status')"
                    without-icon
                    :content-class="'sm:text-right ' . (! $vote->isResigned() ? 'sm:-ml-4' : 'sm:-mr-7')"
                >
                    <x-slot name="text">
                        @if($vote->isResigned())
                            <span class="text-theme-danger-400">@lang('pages.delegates.resigned')</span>
                        @elseif($vote->rank() > Network::delegateCount())
                            <span class="text-theme-secondary-500 dark:text-theme-secondary-700">@lang('pages.delegates.standby')</span>
                        @else
                            <span class="text-theme-success-600">@lang('pages.delegates.active')</span>
                        @endif
                    </x-slot>
                </x-general.entity-header-item>
            </div>
        </div>
    </div>
</div>
