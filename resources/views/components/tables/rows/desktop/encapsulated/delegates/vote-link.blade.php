@props(['model'])

<x-ark-external-link
    :url="$model->voteUrl()"
    :text="trans('pages.wallet.delegate.vote')"
    inner-class="text-sm"
    no-icon
/>
