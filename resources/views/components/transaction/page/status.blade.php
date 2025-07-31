@props(['model'])

@php
    $hasFailedStatus  = $model->hasFailedStatus();
    $transactionError = null;

    if ($hasFailedStatus) {
        $transactionError = $model->parseReceiptError();
        $icon = 'circle.cross';
    } else {
        $icon = 'double-check-mark';
    }
@endphp

<x-general.page-section.container
    :title="trans('pages.transaction.status.header')"
    wrapper-class="flex flex-col flex-1 whitespace-nowrap"
    :wrapper-container-class="Arr::toCssClasses(['py-2 mx-2 rounded-lg border sm:mx-0 sm:py-4',
        'bg-theme-success-100 dark:bg-theme-success-900 border-theme-success-200 dark:border-theme-success-500' => ! $hasFailedStatus,
        'bg-theme-danger-50 dark:bg-transparent border-theme-danger-200 dark:border-theme-danger-400' => $hasFailedStatus,
    ])"
    no-border
>
    <div @class([
        'flex items-center space-x-2 divide-x sm:space-x-3',
        'divide-theme-success-200 dark:divide-theme-success-800' => ! $hasFailedStatus,
        'divide-theme-danger-200 dark:divide-theme-dark-700' => $hasFailedStatus,
    ])>
        <div @class([
            'flex items-center space-x-2 pl-3 sm:pl-6',
            'text-theme-success-700 dark:text-theme-success-500' => ! $hasFailedStatus,
            'text-theme-danger-700 dark:text-theme-danger-400' => $hasFailedStatus,
        ])>
            <x-ark-icon
                :name="$icon"
                size="w-4 h-4 sm:w-5 sm:h-5"
            />

            <div>
                @if ($hasFailedStatus)
                    @lang('general.failed')
                @else
                    @lang('general.success')
                @endif
            </div>
        </div>

        <div class="pl-2 sm:pl-3">
            @if ($model->confirmations() > 1000)
                <x-number>1000</x-number>+ @lang('general.confirmations_only')
            @else
                {{ trans_choice('general.confirmations', $model->confirmations(), ['count' => $model->confirmations()]) }}
            @endif
        </div>

        @if ($hasFailedStatus)
            <div class="hidden pl-2 sm:pl-3 lg:block">
                @if ($transactionError)
                    @lang('pages.transaction.status.failed_message', [
                        'error' => preg_replace('/([A-Z])/', ' \1', $transactionError),
                    ])
                @else
                    @lang('pages.transaction.status.failed_no_message')
                @endif
            </div>
        @endif
    </div>

    @if ($hasFailedStatus)
        <div class="px-3 pt-2 mt-2 whitespace-normal border-t sm:pt-3 sm:pl-6 sm:mt-3 border-theme-danger-200 lg:hidden dark:border-theme-dark-700">
            @if ($transactionError)
                @lang('pages.transaction.status.failed_message', [
                    'error' => preg_replace('/([A-Z])/', ' \1', $transactionError),
                ])
            @else
                @lang('pages.transaction.status.failed_no_message')
            @endif
        </div>
    @endif
</x-general.page-section.container>
