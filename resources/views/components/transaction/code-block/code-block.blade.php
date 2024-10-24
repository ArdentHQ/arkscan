@props(['transaction'])

<div
    class="flex flex-col text-sm"
    x-data="{
        view: 'default',
        get content() {
            return this.$refs[`code-${this.view}`].innerText;
        },
        changeView(view) {
            this.view = view;
        },
    }"
    x-init="changeView('default')"
>
    <div class="flex flex-col justify-between px-4 pt-3 space-y-3 rounded-t-lg sm:flex-row sm:items-center sm:pt-0 sm:space-y-0 sm:h-10 bg-theme-secondary-900 text-theme-secondary-200 shadow-code-block dark:bg-theme-dark-800 dark:text-theme-dark-200">
        <div>
            @lang('pages.transaction.input_data')
        </div>

        <div class="flex h-full sm:justify-between sm:items-center">
            <div class="pr-1.5 leading-3.75 dark:text-theme-dark-200">
                <span>@lang('pages.transaction.code-block.view_input_as')</span>
            </div>

            <x-transaction.code-block.tab value="default" />
            <x-transaction.code-block.tab value="utf-8" />
            <x-transaction.code-block.tab value="original" />
        </div>
    </div>

    <div class="flex overflow-x-auto flex-1 p-4 font-normal bg-black rounded-b-lg code-block-custom-scroll text-[#C3B6FD] text-[13px] shadow-code-block">
        <pre x-ref="code-default" x-show="view == 'default'">{{ $transaction->formattedPayload() }}</pre>
        <pre x-ref="code-utf-8" x-show="view == 'utf-8'">{{ $transaction->utf8Payload() }}</pre>
        <pre x-ref="code-original" x-show="view == 'original'">{{ $transaction->rawPayload() }}</pre>
    </div>

    <x-ark-clipboard
        class="flex items-center mt-4 space-x-2 w-full sm:w-auto button button-secondary"
        alpine-property="content"
        no-styling
    >
        <span>@lang('pages.transaction.code-block.copy_code')</span>
    </x-ark-clipboard>
</div>
