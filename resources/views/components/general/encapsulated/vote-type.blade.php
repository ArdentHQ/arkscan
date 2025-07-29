@props(['transaction'])

@if($transaction->isVote())
    @php($votedValidator = $transaction->voted())

    <span
        @if ($votedValidator)
            data-tippy-html-content="{{ trans('general.transaction.vote_validator', ['validator' => $votedValidator->username() ?? $votedValidator->address()]) }}"
        @endif
    >
        {{ $transaction->typeName() }}
    </span>
@else
    <span>
        {{ $transaction->typeName() }}
    </span>
@endif
