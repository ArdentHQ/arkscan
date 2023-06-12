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

<div class="rounded px-[3px] py-[2px] border border-transparent bg-theme-secondary-200 dark:bg-transparent dark:border-theme-secondary-800 dark:text-theme-secondary-500 font-semibold text-xs leading-[15px]">
    @unless ($isOther)
        @lang('general.transaction.types.'.$model->typeName())
    @else
        @lang('general.transaction.types.other')
    @endunless
</div>
