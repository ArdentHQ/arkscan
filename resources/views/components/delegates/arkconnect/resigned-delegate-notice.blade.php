@if (config('arkscan.arkconnect.enabled'))
    <div
        :class="{ '!hidden': isVotedDelegateResigned !== true }"
        class="mb-4 sm:mb-3"
        x-cloak
    >
        <x-ark-alert type="warning">
            <span>@lang('general.arkconnect.delegate_resigned')</span>

            <span class="font-semibold text-theme-primary-600">
                @lang('pages.delegates.arkconnect.recommend_switch_votes')
            </span>
        </x-ark-alert>
    </div>
@endif
