@props(['transaction'])

@php ($vendorField = $transaction->vendorField())

<x-general.page-section.container
    :title="trans('pages.transaction.memo')"
    wrapper-class="font-normal leading-7 max-w-full"
>
    @if (! $vendorField)
        <span class="text-sm font-semibold sm:text-base sm:font-normal text-theme-secondary-500 dark:text-theme-dark-500">
            @lang('general.na')
        </span>
    @else
        <div class="text-base text-theme-secondary-900 dark:text-theme-dark-50 break-words">
            {{ $vendorField }}
        </div>
    @endif
</x-general.page-section.container>
