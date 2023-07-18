@props(['transaction'])

@php ($vendorField = $transaction->vendorField())

<x-transaction.page-section
    :title="trans('pages.transaction.memo')"
    wrapper-class="font-normal"
>
    @if (! $vendorField)
        @lang('general.na')
    @else
        {{ $vendorField }}
    @endif
</x-transaction.page-section>
