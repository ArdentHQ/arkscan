@props(['transaction'])

@php ($vendorField = $transaction->vendorField())

<x-transaction.page-section
    :title="trans('pages.transaction.memo')"
    wrapper-class="font-normal leading-7"
>
    @if (! $vendorField)
        <span class="text-sm font-semibold sm:font-normal text-theme-secondary-500 dark:text-theme-dark-500">
            @lang('general.na')
        </span>
    @else
        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
            {{ $vendorField }}
        </span>
    @endif
</x-transaction.page-section>
