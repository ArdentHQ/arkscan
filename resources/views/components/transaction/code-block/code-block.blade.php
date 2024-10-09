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
    <div class="rounded-t-lg flex flex-col sm:flex-row justify-between space-y-3 sm:space-y-0 sm:items-center bg-theme-secondary-900 dark:bg-theme-dark-800 text-theme-secondary-200 dark:text-theme-dark-200 pt-3 sm:pt-0 sm:h-10 px-4 shadow-code-block">
        <div>
            @lang('pages.transaction.input_data')
        </div>

        <div class="flex sm:justify-between sm:items-center h-full">
            <div class="dark:text-theme-dark-200 leading-3.75 pr-1.5">
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
