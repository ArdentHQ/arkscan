@props(['transaction'])

<div
    class="flex flex-col text-sm code-block"
    x-data="{
        view: 'default',
        changeView(view) {
            this.view = view;
        }
    }"
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

    <div class="bg-black text-[#C3B6FD] text-[13px] font-normal flex flex-1 rounded-b-lg overflow-x-auto p-4 shadow-code-block">
        <pre x-show="view == 'default'">{{ $transaction->formattedPayload() }}</pre>
        <pre x-show="view == 'utf-8'">{{ $transaction->utf8Payload() }}</pre>
        <pre x-show="view == 'original'">{{ $transaction->rawPayload() }}</pre>
    </div>
</div>
