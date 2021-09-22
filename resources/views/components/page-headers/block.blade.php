<div class="dark:bg-theme-secondary-900">
    <x-ark-container container-class="flex flex-col space-y-6">
        <h1>
            @lang('pages.block.title')
        </h1>

        <x-general.entity-header
            :title="trans('pages.block.block_id')"
            :value="$block->id()"
            padding="lg:pl-8 lg:pr-7 px-7 lg:py-5 py-6"
        >
            <x-slot name="logo">
                <x-page-headers.circle>
                    <x-ark-icon name='app-block-id' />
                </x-page-headers.circle>
            </x-slot>

            @if ($block->previousBlockUrl() || $block->nextBlockUrl())
                <x-slot name="extension">
                    <div class="flex items-center mt-6 space-x-2 lg:mt-0 lg:ml-3 text-theme-secondary-400">
                        @if ($block->previousBlockUrl())
                            <a href="{{ $block->previousBlockUrl() }}" class="flex flex-1 justify-center items-center px-4 h-11 rounded cursor-pointer lg:flex-none bg-theme-secondary-800 transition-default hover:bg-theme-secondary-700">
                                <x-ark-icon name="chevron-left" size="sm" />
                            </a>
                        @endif

                        @if ($block->nextBlockUrl())
                            <a href="{{ $block->nextBlockUrl() }}" class="flex flex-1 justify-center items-center px-4 h-11 rounded cursor-pointer lg:flex-none bg-theme-secondary-800 transition-default hover:bg-theme-secondary-700">
                                <x-ark-icon name="chevron-right" size="sm" />
                            </a>
                        @endif
                    </div>
                </x-slot>
            @endif

            <x-slot name="bottom">
                <div class="grid grid-cols-1 gap-y-8 sm:grid-cols-2 xl:grid-cols-4">
                    <x-general.entity-header-item
                        :title="trans('pages.block.generated_by')"
                        :avatar="$block->username()"
                        :text="$block->username()"
                        icon-size="md"
                        :url="route('wallet', $block->delegate()->address)"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.block.transactions')"
                        icon="app-transactions"
                        icon-size="md"
                        :text="$block->transactionCount()"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.block.transaction_volume')"
                        icon="app-supply"
                    >
                        <x-slot name="text">
                            <x-currency :currency="Network::currency()">{{ $block->amount() }}</x-currency>
                        </x-slot>
                    </x-general.entity-header-item>

                    <x-general.entity-header-item icon="app-reward" icon-size="md">
                        <x-slot name="title">
                            <span data-tippy-content="@lang('pages.block.total_rewards_tooltip', [$block->reward()])">
                                @lang('pages.block.total_rewards')
                            </span>
                        </x-slot>

                        <x-slot name="text">
                            <x-currency :currency="Network::currency()">{{ $block->totalReward() }}</x-currency>
                        </x-slot>
                    </x-general.entity-header-item>
                </div>
            </x-slot>
        </x-general.entity-header>
    </x-ark-container>
</div>
