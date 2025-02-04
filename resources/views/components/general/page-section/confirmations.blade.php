@props(['model'])

<x-general.page-section.container
    :title="trans('pages.transaction.status.header')"
    border-class="border-theme-success-200 dark:border-theme-success-500"
    wrapper-container-class="py-2 mx-2 rounded-lg border sm:mx-0 bg-theme-success-100 dark:bg-theme-success-900"
>
    <div class="flex items-center space-x-2 divide-x sm:space-x-3 divide-theme-success-200 dark:divide-theme-success-800">
        <div class="flex items-center space-x-2 text-theme-success-700 dark:text-theme-success-500">
            <x-ark-icon
                name="double-check-mark"
                size="w-4 h-4 sm:w-5 sm:h-5"
            />

            <div>@lang('general.success')</div>
        </div>

        <div class="pl-2 sm:pl-3 dark:text-theme-dark-50">
            @if ($model->confirmations() > 1000)
                <x-number>1000</x-number>+ @lang('general.confirmations_only')
            @else
                {{ @trans_choice('general.confirmations', $model->confirmations(), ['count' => $model->confirmations()]) }}
            @endif
        </div>
    </div>
</x-general.page-section.container>
