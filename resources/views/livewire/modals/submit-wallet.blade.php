<div class="w-full sm:w-auto">
    <button
        type="button"
        class="w-full sm:w-auto button-primary"
        wire:click="openModal"
    >
        @lang('actions.submit_wallet')
    </button>

    @if($this->modalShown)
        <x-modals.modal :title="trans('pages.compatible-wallets.submit-modal.title')">
            <x-slot name="description">
                <div class="px-6 -mx-6 mt-1 font-normal text-theme-secondary-700 dark:text-theme-dark-200 pt-4 border-t border-theme-secondary-300 dark:border-theme-dark-700 flex flex-col space-y-4">
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
        </x-modals.modal>
    @endif
</div>
