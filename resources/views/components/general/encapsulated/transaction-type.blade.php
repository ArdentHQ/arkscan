@props(['transaction'])

@php
    $isVoteType = in_array($transaction->typeName(), [
        'vote',
        'unvote',
    ]);
@endphp

@unless ($transaction->isLegacy())
    @if ($isVoteType)
        <x-general.encapsulated.vote-type :transaction="$transaction" />
    @else
        @lang('general.transaction.types.'.$transaction->typeName())
    @endif
@else
    @lang('general.transaction.types.legacy')
@endunless
