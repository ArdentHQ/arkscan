<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="py-16 space-x-4 content-container md:px-8">
        {{-- Vote --}}
        <div class="w-1/2">
            <div class="relative flex items-end justify-between mb-8">
                <h2 class="text-3xl sm:text-4xl">@lang('pages.transaction.vote')</h2>
            </div>

            <x-details.vote
                :title="trans('general.transaction.delegate')"
                :transaction="$transaction"
                :address="$transaction->voted()->address()">
                <x-slot name="icon">
                    <div class="circled-icon text-theme-success-500 border-theme-success-100 dark:text-theme-success-600 dark:border-theme-success-600">
                        @svg('app-transactions.vote', 'h-5 w-5 text-theme-success-500')
                    </div>
                </x-slot>
            </x-details.vote>
        </div>

        {{-- Unvote --}}
        <div class="w-1/2">
            <div class="relative flex items-end justify-between mb-8">
                <h2 class="text-3xl sm:text-4xl">@lang('pages.transaction.unvote')</h2>
            </div>

            <x-details.vote
                :title="trans('general.transaction.delegate')"
                :transaction="$transaction"
                :address="$transaction->unvoted()->address()">
                <x-slot name="icon">
                    <div class="circled-icon text-theme-danger-500 border-theme-danger-100 dark:text-theme-danger-600 dark:border-theme-danger-600">
                        @svg('app-transactions.unvote', 'h-5 w-5 text-theme-danger-500')
                    </div>
                </x-slot>
            </x-details.vote>
        </div>
    </div>
</div>
