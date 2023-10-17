<div class="pb-6 md:pb-0">
    <div class="content-container-full-width">
        <div class="px-6 md:px-10 w-full">
            <div
                wire:poll.{{ $refreshInterval }}s
                class="grid gap-2 grid-cols-1 w-full sm:grid-cols-2 md:gap-3 xl:grid-cols-4"
            >
                <x-stats.stat :label="trans('pages.statistics.highlights.total_supply')">
                    <x-currency :currency="Network::currency()">{{ $totalSupply }}</x-currency>
                </x-stats.stat>

                <x-stats.stat :label="trans('pages.statistics.highlights.voting', ['percent' => $votingPercent])">
                   <x-currency :currency="Network::currency()">{{ $votingValue }}</x-currency>
                </x-stats.stat>

                <x-stats.stat :label="trans('pages.statistics.highlights.delegates')">
                    <span>{{ $delegates }}</span>

                    <div class="pl-3">
                        <a
                            href="{{ route('delegates') }}"
                            class="link"
                        >
                            @lang('actions.view_all')
                        </a>
                    </div>
                </x-stats.stat>

                <x-stats.stat :label="trans('pages.statistics.highlights.wallets')">
                    <span>{{ $wallets }}</span>

                    <div class="pl-3">
                        <a
                            href="{{ route('top-accounts') }}"
                            class="link"
                        >
                            @lang('actions.view_all')
                        </a>
                    </div>
                </x-stats.stat>
            </div>
        </div>
    </div>
</div>
