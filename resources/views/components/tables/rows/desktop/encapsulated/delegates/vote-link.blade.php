@props(['model'])

@if (config('arkscan.arkconnect.enabled', false))
    <x-delegates.arkconnect.vote-link :model="$model" />
@else
    @if (config('arkscan.arkconnect.enabled', false))
        <x-delegates.arkconnect.vote-link :model="$model" />
    @else
        <x-ark-external-link
            :url="$model->voteUrl()"
            :text="trans('actions.vote')"
            inner-class="text-sm"
            no-icon
        />
    @endif
@endif
