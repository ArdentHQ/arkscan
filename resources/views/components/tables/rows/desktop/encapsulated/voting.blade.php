@props([
    'model'
])

@if($model->isVoting())
    <div
        class="flex justify-center items-center w-full"
        @if ($model->vote())
            data-tippy-html-content="{{ trans('general.transaction.voting_validator', ['validator' => $model->vote()->address()]) }}"
        @endif
    >
        <x-ark-icon name="check-mark-box" size="sm" />
    </div>
@endif
