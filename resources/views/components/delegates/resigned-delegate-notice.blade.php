@if (config('arkscan.arkconnect.enabled'))
    <div
        x-data="{
            isResigned: false,
        }"
        x-init="$watch('isVotedDelegateResigned', (value) => isResigned = isVotedDelegateResigned)"
        :class="{ '!hidden': isResigned === false }"
        class="mb-4 sm:mb-3"
        x-cloak
    >
        <x-ark-alert type="warning">
            <span
                x-text="votedDelegateName"
                class="font-semibold"
            ></span><span>@lang('pages.delegates.arkconnect.resigned_delegate')</span> {{-- Kept on the same line to keep text next to each other --}}

            <span class="font-semibold text-theme-primary-600">
                @lang('pages.delegates.arkconnect.recommend_switch_votes')
            </span>
        </x-ark-alert>
    </div>
@endif
