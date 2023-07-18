@props(['transaction'])

<x-transaction.page-section
    :title="trans('pages.transaction.confirmations')"
    border-class="border-theme-success-200 dark:border-theme-success-500"
    wrapper-container-class="mx-2 sm:mx-0 bg-theme-success-100 dark:bg-theme-success-900 py-2 rounded-lg border"
>
    <div class="flex space-x-2 sm:space-x-3 items-center divide-x divide-theme-success-200 dark:divide-theme-success-800">
        <div class="flex items-center space-x-2 text-theme-success-700 dark:text-theme-success-500">
            <x-ark-icon
                name="double-check-mark"
                size="w-4 h-4 sm:w-5 sm:h-5"
            />

            <div>@lang('general.success')</div>
        </div>

        <div class="pl-2 sm:pl-3 dark:text-theme-dark-50">
            @if ($transaction->confirmations() > 1000)
                <x-number>1000</x-number>+ @lang('general.confirmations_only')
            @else
                {{ @trans_choice('general.confirmations', $transaction->confirmations(), ['count' => $transaction->confirmations()]) }}
            @endif
        </div>
    </div>
</x-transaction.page-section>
