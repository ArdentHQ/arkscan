<div x-data="{ visible: false, options: false }" x-cloak>
    <div x-show="visible">
        <x-ark-modal class="w-full mx-6" width-class="max-w-sm" alpine-close="visible = false" title-class="header-2">
            @slot('title')
                @lang('pages.wallet.qrcode.title')
            @endslot

            @slot('description')
                @lang('pages.wallet.qrcode.description')

                <div class="mt-8 border border-theme-secondary-400">
                    {!! $this->code !!}
                </div>

                <div x-show="options">
                    <x-ark-input
                        :label="trans('pages.wallet.amount')"
                        class="mt-5"
                        type="text"
                        id="amount"
                        name="amount"
                        required
                        autofocus
                    />

                    <x-ark-input
                        :label="trans('pages.wallet.smartbridge')"
                        class="mt-5"
                        type="text"
                        id="smartbridge"
                        name="smartbridge"
                        required
                    />
                </div>
            @endslot

            @slot('buttons')
                <div class="flex justify-end mt-5 space-x-3">
                    <button x-show="options" class="button-secondary" @click="options = false">
                        @lang('pages.wallet.hide_options')
                    </button>

                    <button x-show="! options" class="button-secondary" @click="options = true">
                        @lang('pages.wallet.show_options')
                    </button>
                </div>
            @endslot
        </x-ark-modal>
    </div>
</div>
