<div
    x-show="! hasExtension"
    x-data="Modal.alpine({ shown: false })"
    x-cloak
>
    <x-arkconnect.modal.modal>
        <x-slot name="title">
            @lang('general.navbar.arkconnect.modal.install_title')

            <div class="text-sm font-normal text-theme-secondary-700 leading-5.25 mt-1.5">
                @lang('general.navbar.arkconnect.modal.install_subtitle')
            </div>
        </x-slot>

        <x-slot name="description">
            <div class="w-[301px]">
                <img src="{{ Vite::image('modals/arkconnect/install.svg') }}" class="dark:hidden" />
                <img src="{{ Vite::image('modals/arkconnect/install-dark.svg') }}" class="hidden dark:block dim:hidden" />
                <img src="{{ Vite::image('modals/arkconnect/install-dim.svg') }}" class="hidden dim:block" />
            </div>
        </x-slot>

        <x-slot name="button">
            <a
                href="@lang('urls.arkconnect')"
                target="_blank"
                rel="noopener nofollow noreferrer"
                class="button-primary"
            >
                <div class="flex items-center space-x-2 leading-5">
                    <x-ark-icon
                        name="app-wallets.arkconnect"
                        size="sm"
                    />

                    <span>@lang('general.navbar.arkconnect.modal.install_arkconnect')</span>
                </div>
            </a>
        </x-slot>
    </x-arkconnect.modal.modal>

    <button
        class="py-1.5 px-4 whitespace-nowrap button-secondary w-full md:w-auto"
        @click="shown = true"
    >
        @lang('general.navbar.connect_wallet')
    </button>
</div>
