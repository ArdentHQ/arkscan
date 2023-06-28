
@props(['transaction'])

@if($transaction->isVoteCombination())
    <span
        data-tippy-html-content="{{ trans('general.transaction.vote_swap_delegate', ['delegate_vote' => $transaction->voted()->username(), 'delegate_unvote' => $transaction->unvoted()->username()]) }}"
    >
        @lang('general.transaction.types.'.$transaction->typeName())
    </span>
@elseif($transaction->isVote())
    <span
        data-tippy-html-content="{{ trans('general.transaction.vote_delegate', ['delegate' => $transaction->voted()->username()]) }}"
    >
        @lang('general.transaction.types.'.$transaction->typeName())
    </span>
@else
    <span
        data-tippy-html-content="{{ trans('general.transaction.unvote_delegate', ['delegate' => $transaction->unvoted()->username()]) }}"
    >
        @lang('general.transaction.types.'.$transaction->typeName())
    </span>
@endif
