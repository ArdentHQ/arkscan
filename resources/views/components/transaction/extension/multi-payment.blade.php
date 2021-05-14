<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="py-16 content-container">
        <div class="w-full">
            <div class="flex relative justify-between items-end mb-5">
                <h4>@lang('pages.transaction.recipient_list')</h4>
            </div>

            <x-tables.payments :payments="$transaction->payments()" />
        </div>
    </div>
</div>
