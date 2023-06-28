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

@php
    $isVoteType = in_array($transaction->typeName(), [
        'vote',
        'unvote',
        'vote-combination',
    ]);
@endphp

@unless ($isLegacy)
    @if ($isVoteType)
        <x-general.encapsulated.vote-type :transaction="$transaction" />
    @else
        @lang('general.transaction.types.'.$transaction->typeName())
    @endif
@else
    @lang('general.transaction.types.legacy')
@endunless
