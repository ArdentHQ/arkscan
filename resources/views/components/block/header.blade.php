<div class="dark:bg-theme-secondary-900">
    <div class="flex-col py-16 space-y-6 content-container">
        <x-general.search.header-slim :title="trans('pages.block.title')" />

        <x-general.entity-header
            :title="trans('pages.block.block_id')"
            :value="$block->id()"
        >
            <x-slot name="logo">@svg('app-block-id', 'w-5 h-5')</x-slot>

            <x-slot name="extra">
                <div class="flex items-center mt-6 space-x-2 text-theme-secondary-400 md:mt-0">
                    @if ($block->previousBlockUrl())
                        <a href="{{ $block->previousBlockUrl() }}" class="flex items-center justify-center flex-1 h-full px-3 rounded cursor-pointer bg-theme-secondary-800 hover:bg-theme-secondary-700 transition-default md:flex-none">
                            @svg('chevron-left', 'w-6 h-6')
                        </a>
                    @endif

                    @if ($block->nextBlockUrl())
                        <a href="{{ $block->nextBlockUrl() }}" class="flex items-center justify-center flex-1 h-full px-3 rounded cursor-pointer bg-theme-secondary-800 hover:bg-theme-secondary-700 transition-default md:flex-none">
                            @svg('chevron-right', 'w-6 h-6')
                        </a>
                    @endif
                </div>
            </x-slot>

            <x-slot name="bottom">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-4">
                    <x-general.entity-header-item
                        :title="trans('pages.block.generated_by')"
                        :avatar="$block->delegateUsername()"
                        :text="$block->delegateUsername()"
                        :url="route('wallet', $block->delegate()->address)"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.block.transaction_volumn')"
                        icon="app-votes"
                        :text="$block->amount()"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.block.transactions')"
                        icon="exchange"
                        :text="$block->transactionCount()"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.block.total_rewards')"
                        icon="app-reward"
                        :text="$block->reward()"
                    />
                </div>
            </x-slot>
        </x-general.entity-header>
    </div>
</div>
