<div class="w-full sm:w-auto">
    <button
        type="button"
        class="w-full sm:w-auto button-primary"
        wire:click="openModal"
    >
        @lang('actions.submit_wallet')
    </button>

    @if($this->modalShown)
        <x-ark-modal
            :title="trans('pages.compatible-wallets.submit-modal.title')"
            title-class="text-lg text-left sm:text-2xl"
            padding-class="p-6 sm:p-10"
            wire-close="cancel"
            close-button-class="absolute top-0 right-0 p-0 mt-0 mr-0 w-8 h-8 rounded-none sm:mt-6 sm:mr-6 sm:rounded button button-secondary text-theme-secondary-900 dim:bg-transparent dim:shadow-none hover:dark:text-theme-dark-50 hover:dark:bg-theme-dark-blue-600"
            buttons-style="flex flex-col sm:flex-row sm:justify-end !mt-6 sm:space-x-3 space-y-3 sm:space-y-0"
            breakpoint="sm"
            wrapper-class="max-w-full sm:max-w-[430px]"
            content-class="relative bg-white sm:mx-auto sm:shadow-2xl sm:rounded-2.5xl dark:bg-theme-dark-900"
        >
            <x-slot name="description">
                <div class="flex flex-col pt-2 space-y-5">
                    <x-ark-input
                        name="name"
                        :label="trans('pages.compatible-wallets.submit-modal.name')"
                        autocomplete="none"
                    />

                    <x-ark-input
                        name="website"
                        :label="trans('pages.compatible-wallets.submit-modal.website')"
                        autocomplete="none"
                        :placeholder="trans('pages.compatible-wallets.submit-modal.website_placeholder')"
                    />

                    <x-ark-textarea
                        name="message"
                        :label="trans('pages.compatible-wallets.submit-modal.message')"
                        rows="4"
                    />
                </div>
            </x-slot>

            <x-slot name="buttons">
                <button
                    type="button"
                    class="button-secondary"
                    wire:click="cancel"
                >
                    @lang('actions.cancel')
                </button>

                <button
                    type="button"
                    class="button-primary"
                    wire:click="submit"
                    @if ($this->canSubmit)
                        disabled
                    @endif
                >
                    @lang('actions.submit')
                </button>
            </x-slot>
        </x-ark-modal>
    @endif
</div>
