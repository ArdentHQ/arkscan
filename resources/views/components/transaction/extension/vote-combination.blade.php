<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="flex-col py-16 space-y-6 sm:space-y-0 sm:space-x-6 content-container md:px-8 sm:flex-row">
        {{-- Vote --}}
        <div class="w-full sm:w-1/2">
            <div class="relative flex items-end justify-between mb-6">
                <h2 class="text-xl sm:text-2xl">@lang('pages.transaction.vote')</h2>
            </div>

            <x-details.vote
                :title="trans('general.transaction.delegate')"
                :transaction="$transaction"
                :model="$transaction->voted()">
                <x-slot name="icon">
                    <div class="circled-icon text-theme-success-500 border-theme-success-100 dark:text-theme-success-600 dark:border-theme-success-600">
                        <x-icon name="app-transactions.vote" style="success" />
                    </div>
                </x-slot>
            </x-details.vote>
        </div>

        {{-- Unvote --}}
        <div class="w-full sm:w-1/2">
            <div class="relative flex items-end justify-between mb-6">
                <h2 class="text-xl sm:text-2xl">@lang('pages.transaction.unvote')</h2>
            </div>

            <x-details.vote
                :title="trans('general.transaction.delegate')"
                :transaction="$transaction"
                :model="$transaction->unvoted()">
                <x-slot name="icon">
                    <div class="circled-icon text-theme-danger-500 border-theme-danger-100 dark:text-theme-danger-600 dark:border-theme-danger-600">
                        <x-icon name="app-transactions.unvote" style="danger" />
                    </div>
                </x-slot>
            </x-details.vote>
        </div>
    </div>
</div>
