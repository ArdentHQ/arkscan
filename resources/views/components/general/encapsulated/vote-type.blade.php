@props(['transaction'])

@if($transaction->isVoteCombination())
    <span
        data-tippy-html-content="{{ trans('general.transaction.vote_swap_validator', ['validator_vote' => $transaction->voted()->username(), 'validator_unvote' => $transaction->unvoted()->username()]) }}"
    >
        @lang('general.transaction.types.'.$transaction->typeName())
    </span>
@elseif($transaction->isVote())
    <span
        {{-- data-tippy-html-content="{{ trans('general.transaction.vote_validator', ['validator' => $transaction->voted()->username()]) }}" --}}
    >
        @lang('general.transaction.types.'.$transaction->typeName())
    </span>
@else
    <span
        {{-- data-tippy-html-content="{{ trans('general.transaction.unvote_validator', ['validator' => $transaction->unvoted()->username()]) }}" --}}
    >
        @lang('general.transaction.types.'.$transaction->typeName())
    </span>
@endif
