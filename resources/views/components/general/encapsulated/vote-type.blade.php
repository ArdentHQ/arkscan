@props(['transaction'])

@if($transaction->isVote())
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
