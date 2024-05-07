<div
    x-show="! isSupported"
    x-data="Modal.alpine({ shown: false })"
    x-cloak
>
    <x-arkconnect.modal.modal :title="trans('general.navbar.arkconnect.modal.unsupported_browser_title')">
        <x-slot
            name="description"
            class="flex-col space-y-4 sm:space-y-6"
        >
            <div class="h-[104px]">
                <img src="{{ Vite::image('modals/arkconnect/unsupported.svg') }}" class="dark:hidden" />
                <img src="{{ Vite::image('modals/arkconnect/unsupported-dark.svg') }}" class="hidden dark:block dim:hidden" />
                <img src="{{ Vite::image('modals/arkconnect/unsupported-dim.svg') }}" class="hidden dim:block" />
            </div>

            <x-ark-alert type="warning">
                @lang('general.navbar.arkconnect.modal.unsupported_browser_warning')
            </x-ark-alert>
        </x-slot>

        <x-slot name="button">
            <a
                href="@lang('urls.arkconnect')"
                target="_blank"
                rel="noopener nofollow noreferrer"
                class="button-primary"
            >
                @lang('actions.learn_more')
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
