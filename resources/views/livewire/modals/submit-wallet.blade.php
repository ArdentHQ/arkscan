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
                <div class="flex flex-col px-6 pt-4 -mx-6 mt-1 space-y-4 font-normal border-t text-theme-secondary-700 border-theme-secondary-300 dark:text-theme-dark-200 dark:border-theme-dark-700">
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
