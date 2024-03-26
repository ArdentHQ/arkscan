@props(['model'])

@if ($model->isResigned())
    <div data-tippy-content="@lang('pages.wallet.validator.resigned_vote_tooltip')">
        <button
            type="button"
            href="javascript:void(0)"
            class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-dark-500"
            disabled
        >
            @lang('pages.wallet.validator.vote')
        </button>
    </div>
@else
    <x-ark-external-link
        :url="$model->voteUrl()"
        :text="trans('pages.wallet.validator.vote')"
        inner-class="text-sm"
        no-icon
    />
@endif
