{{-- TODO: proper styling with green/red icon and avatar overlap --}}
<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="py-16 space-x-4 content-container md:px-8">
        {{-- Vote --}}
        <div class="w-1/2">
            <div class="relative flex items-end justify-between mb-8">
                <h2 class="text-3xl sm:text-4xl">@lang('pages.transaction.vote')</h2>
            </div>

            <x-details.address
                :title="trans('general.transaction.delegate')"
                :transaction="$transaction"
                :address="$transaction->voted()->address"
                icon="app-volume" />
        </div>

        {{-- Unvote --}}
        <div class="w-1/2">
            <div class="relative flex items-end justify-between mb-8">
                <h2 class="text-3xl sm:text-4xl">@lang('pages.transaction.unvote')</h2>
            </div>

            <x-details.address
                :title="trans('general.transaction.delegate')"
                :transaction="$transaction"
                :address="$transaction->unvoted()->address"
                icon="app-volume" />
        </div>
    </div>
</div>
