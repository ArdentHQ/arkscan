<div class="dark:bg-theme-secondary-900">
    <div class="flex-col pt-16 mb-16 space-y-6 content-container">
        <x-general.search.header-slim :title="trans($title)" />

        <x-general.entity-header :value="$wallet->address()">
            <x-slot name="title">
                @if($wallet->isDelegate())
                    @lang('pages.wallet.address_delegate', [$wallet->username()])
                @else
                    @lang('pages.wallet.address')
                @endif
            </x-slot>

            <x-slot name="logo">
                @if($wallet->isDelegate())
                    <x-headings.avatar-with-icon :model="$wallet" icon="app-delegate" />
                @else
                    <x-headings.avatar :model="$wallet" />
                @endif
            </x-slot>

            <x-slot name="extra">
                {{ $slot }}

                <div class="flex items-center mt-6 space-x-2 text-theme-secondary-200 lg:mt-0">
                    {{-- @TODO: public key button --}}
                    <a href="#" class="flex items-center justify-center flex-1 w-16 px-3 rounded cursor-pointer bg-theme-secondary-800 hover:bg-theme-primary-600 transition-default lg:flex-none h-11">
                        @svg('key', 'w-6 h-6')
                    </a>

                    <button @click="livewire.emit('toggleQrCode')" type="button" class="flex items-center justify-center flex-1 w-16 px-3 rounded cursor-pointer bg-theme-primary-600 hover:bg-theme-primary-700 transition-default lg:flex-none h-11">
                        @svg('app-qr-code', 'w-6 h-6')
                    </button>
                </div>
            </x-slot>

            @isset($extra)
                {{ $extra }}
            @endisset
        </x-general.entity-header>
    </div>
</div>
