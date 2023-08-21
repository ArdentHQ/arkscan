@props(['model'])

@if ($model->isResigned())
    <div data-tippy-content="@lang('pages.wallet.delegate.resigned_vote_tooltip')">
        <button
            type="button"
            href="javascript:void(0)"
            class="text-theme-secondary-500 dark:text-theme-dark-500 text-sm font-semibold"
            disabled
        >
            @lang('pages.wallet.delegate.vote')
        </button>
    </div>
@else
    <x-ark-external-link
        :url="$model->voteUrl()"
        :text="trans('pages.wallet.delegate.vote')"
        inner-class="text-sm"
        no-icon
    />
@endif
