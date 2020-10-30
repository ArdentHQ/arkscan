<div class="dark:bg-theme-secondary-900">
    <div class="flex-col pt-16 space-y-6 content-container">
        <x-general.search.header-slim :title="trans('pages.block.title')" />

        <x-general.entity-header
            :title="trans('pages.block.block_id')"
            :value="$block->id()"
            is-block-page
        >
            <x-slot name="logo">
                <x-headings.circle>
                    @svg('app-block-id', 'w-5 h-5')
                </x-headings.circle>
            </x-slot>

            <x-slot name="extra">
                <div class="flex items-center mt-6 space-x-2 text-theme-secondary-400 md:mt-0">
                    @if ($block->previousBlockUrl())
                        <a href="{{ $block->previousBlockUrl() }}" class="flex items-center justify-center flex-1 px-3 rounded cursor-pointer h-11 bg-theme-secondary-800 hover:bg-theme-secondary-700 transition-default md:flex-none">
                            @svg('chevron-left', 'w-6 h-6')
                        </a>
                    @endif

                    @if ($block->nextBlockUrl())
                        <a href="{{ $block->nextBlockUrl() }}" class="flex items-center justify-center flex-1 px-3 rounded cursor-pointer h-11 bg-theme-secondary-800 hover:bg-theme-secondary-700 transition-default md:flex-none">
                            @svg('chevron-right', 'w-6 h-6')
                        </a>
                    @endif
                </div>
            </x-slot>

            <x-slot name="bottom">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-4">
                    <x-general.entity-header-item
                        :title="trans('pages.block.generated_by')"
                        :avatar="$block->username()"
                        :text="$block->username()"
                        :url="route('wallet', $block->delegate()->address)"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.block.transactions')"
                        icon="exchange"
                        :text="$block->transactionCount()"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.block.transaction_volume')"
                        icon="app-votes"
                    >
                        <x-slot name="text">
                            <x-currency>{{ $block->amount() }}</x-currency>
                        </x-slot>
                    </x-general.amount-fiat-tooltip>

                    <x-general.entity-header-item icon="app-reward">
                        <x-slot name="title">
                            <span data-tippy-content="@lang('pages.block.total_rewards_tooltip', [$block->reward()])">
                                @lang('pages.block.total_rewards')
                            </span>
                        </x-slot>

                        <x-slot name="text">
                            <x-currency>{{ $block->totalReward() }}</x-currency>
                        </x-slot>
                    </x-general.entity-header-item>
                </div>
            </x-slot>
        </x-general.entity-header>
    </div>
</div>
