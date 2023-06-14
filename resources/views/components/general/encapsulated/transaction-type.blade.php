@props(['transaction'])

@php
    $isLegacy = ! in_array($transaction->typeName(), [
        'delegate-registration',
        'delegate-resignation',
        'ipfs',
        'multi-payment',
        'vote-combination',
        'second-signature',
        'transfer',
        'unvote',
        'vote',
        'multi-signature',
    ]);
@endphp

@unless ($isLegacy)
    @lang('general.transaction.types.'.$transaction->typeName())
@else
    @lang('general.transaction.types.legacy')
@endunless
