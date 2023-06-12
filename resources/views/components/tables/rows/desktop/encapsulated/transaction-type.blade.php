@props(['model'])

@php
    $isOther = ! in_array($model->typeName(), [
        'delegate-registration',
        'delegate-resignation',
        'ipfs',
        'multi-payment',
        'vote-combination',
        'second-signature',
        'transfer',
        'unvote',
        'vote',
    ]);
@endphp

<div class="text-xs font-semibold rounded border border-transparent dark:bg-transparent px-[3px] py-[2px] bg-theme-secondary-200 leading-[15px] dark:border-theme-secondary-800 dark:text-theme-secondary-500">
    @unless ($isOther)
        @lang('general.transaction.types.'.$model->typeName())
    @else
        @lang('general.transaction.types.other')
    @endunless
</div>
