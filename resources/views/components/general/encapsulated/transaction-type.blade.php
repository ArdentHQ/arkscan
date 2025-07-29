@props(['transaction'])

@php
    $isVoteType = in_array($transaction->typeName(), [
        'Vote',
        'Unvote',
    ]);
@endphp

@if ($isVoteType)
    <x-general.encapsulated.vote-type :transaction="$transaction" />
@else
    {{ $transaction->typeName() }}
@endif
