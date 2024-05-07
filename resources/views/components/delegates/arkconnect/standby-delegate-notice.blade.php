@if (config('arkscan.arkconnect.enabled'))
    <div
        :class="{ '!hidden': isVotedDelegateOnStandby !== true }"
        class="mb-4 sm:mb-3"
        x-cloak
    >
        <x-ark-alert type="info">
            <span>@lang('general.arkconnect.delegate_standby')</span>

            <span class="font-semibold text-theme-primary-600">
                @lang('pages.delegates.arkconnect.recommend_switch_votes')
            </span>
        </x-ark-alert>
    </div>
@endif
