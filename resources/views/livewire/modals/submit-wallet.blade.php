<div>
    <button
        type="button"
        class="mt-3 w-full sm:mt-0 sm:w-auto button-primary"
        wire:click="openModal"
    >
        @lang('actions.submit_wallet')
    </button>

    @if($this->modalShown)
        <x-ark-modal
            :title="trans('pages.compatible-wallets.submit-modal.title')"
            title-class="text-left text-lg sm:text-2xl"
            padding-class="p-6 sm:p-10"
            wire-close="closeModal"
            close-button-class="absolute top-0 right-0 w-11 h-11 rounded-none sm:rounded mt-0 mr-0 sm:mt-6 sm:mr-6 button button-secondary p-0"
            buttons-style="flex flex-col sm:flex-row sm:justify-end !mt-6 sm:space-x-3 space-y-3 sm:space-y-0"
            breakpoint="sm"
            wrapper-class="max-w-full sm:max-w-[430px]"
            content-class="relative bg-white sm:mx-auto sm:rounded-2.5xl sm:shadow-2xl dark:bg-theme-secondary-900"
        >
            <x-slot name="description">
                <div class="flex flex-col space-y-5 pt-2">
                    <x-ark-input
                        name="name"
                        :label="trans('pages.compatible-wallets.submit-modal.name')"
                        autocomplete="none"
                    />

                    <x-ark-input
                        name="website"
                        :label="trans('pages.compatible-wallets.submit-modal.website')"
                        autocomplete="none"
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
