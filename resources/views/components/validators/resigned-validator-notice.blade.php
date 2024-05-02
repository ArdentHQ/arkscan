@if (config('arkscan.arkconnect.enabled'))
    <div
        :class="{ '!hidden': isVotedValidatorResigned !== true }"
        class="mb-4 sm:mb-3"
        x-cloak
    >
        <x-ark-alert type="warning">
            <span
                x-text="votedValidatorName"
                class="font-semibold"
            ></span><span>@lang('pages.validators.arkconnect.resigned_validator')</span> {{-- Kept on the same line to keep text next to each other --}}

            <span class="font-semibold text-theme-primary-600">
                @lang('pages.validators.arkconnect.recommend_switch_votes')
            </span>
        </x-ark-alert>
    </div>
@endif
