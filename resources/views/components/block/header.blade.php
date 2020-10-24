<div class="dark:bg-theme-secondary-900">
    <div class="flex-col py-16 space-y-6 content-container">
        <x-general.search.header-slim :title="trans('pages.block.title')" />

        <x-general.entity-header
            :title="trans('pages.block.block_id')"
            value="5e665af8f9805b0a8171b5badf6289ddff96e110e512725132c6f4e3dffea94e"
        >
            <x-slot name="logo">@svg('app-block-id', 'w-5 h-5')</x-slot>

            <x-slot name="extra">
                <div class="flex items-center mt-6 space-x-2 text-theme-secondary-400 md:mt-0">
                    <div class="flex items-center justify-center flex-1 h-full px-3 rounded cursor-pointer bg-theme-secondary-800 hover:bg-theme-secondary-700 transition-default md:flex-none">
                        @svg('chevron-left', 'w-6 h-6')
                    </div>

                    <div class="flex items-center justify-center flex-1 h-full px-3 rounded cursor-pointer bg-theme-secondary-800 hover:bg-theme-secondary-700 transition-default md:flex-none">
                        @svg('chevron-right', 'w-6 h-6')
                    </div>
                </div>
            </x-slot>

            <x-slot name="bottom">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-4">
                    <x-general.entity-header-item
                        :title="trans('pages.block.generated_by')"
                        avatar="delegate_name"
                        text="cams_yellow_jacket"
                        url="asd"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.block.transaction_volumn')"
                        icon="app-votes"
                        text="475.133 ARK"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.block.transactions')"
                        icon="exchange"
                        text="3"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.block.total_rewards')"
                        icon="app-reward"
                        text="2.073541 ARK"
                    />
                </div>
            </x-slot>
        </x-general.entity-header>
    </div>
</div>
