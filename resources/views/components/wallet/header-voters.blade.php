<div class="mb-16 dark:bg-theme-secondary-900">
    <div class="flex-col pt-16 space-y-6 content-container">
        <x-general.search.header-slim :title="trans('pages.voters_by_wallet.title')" />

        <x-general.entity-header
            :title="trans('pages.wallet.address')"
            :value="$wallet->address()"
        >
            <x-slot name="logo">
                <x-headings.avatar-with-icon :model="$wallet" icon="app-delegate" />
            </x-slot>

            <x-slot name="extra">
                <div class="flex justify-between flex-1 pl-4 font-semibold border-l md:ml-8 border-theme-secondary-800">
                    <div class="items-center hidden md:flex">
                        <div class="circled-icon text-theme-secondary-700 border-theme-secondary-800">
                            @svg('app-transactions.vote', 'w-5 h-5')
                        </div>
                    </div>

                    <div class="flex flex-col justify-between flex-1 pl-4">
                        <div class="text-sm leading-tight text-theme-secondary-600 dark:text-theme-secondary-700">
                            @lang('pages.wallet.delegate.voters')
                        </div>

                        <div class="flex items-center space-x-2 leading-tight">
                            <span class="truncate text-theme-secondary-400 dark:text-theme-secondary-200">
                                <x-number>{{ $wallet->voterCount() }}</x-number>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center mt-6 space-x-2 text-theme-secondary-200 md:mt-0">
                    {{-- @TODO: public key button --}}
                    <a href="#" class="flex items-center justify-center flex-1 h-full px-3 rounded cursor-pointer bg-theme-secondary-800 hover:bg-theme-primary-600 transition-default md:flex-none">
                        @svg('app-key', 'w-6 h-6')
                    </a>

                    <button @click="livewire.emit('toggleQrCode')" type="button" class="flex items-center justify-center flex-1 h-full px-3 rounded cursor-pointer bg-theme-secondary-800 hover:bg-theme-primary-600 transition-default md:flex-none">
                        @svg('app-qr-code', 'w-6 h-6')
                    </button>
                </div>
            </x-slot>
        </x-general.entity-header>
    </div>
</div>
