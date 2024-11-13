@props(['transaction'])

@php
    $isVoteType = in_array($transaction->typeName(), [
        'vote',
        'unvote',
    ]);
@endphp

@if ($isVoteType)
    <x-general.encapsulated.vote-type :transaction="$transaction" />
@else
    @lang('general.transaction.types.'.$transaction->typeName())
@endif
