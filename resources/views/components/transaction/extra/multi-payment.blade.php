<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="py-16 content-container md:px-8">
        <div class="w-full">
            <div class="relative flex items-end justify-between mb-8">
                <h2 class="text-xl sm:text-2xl">@lang('pages.transaction.recipient_list')</h2>
            </div>

            <x-tables.payments :payments="$transaction->payments()" />
        </div>
    </div>
</div>
