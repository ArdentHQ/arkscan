@props([
    'model',
    'voteText' => trans('actions.vote'),
    'unvoteText' => trans('actions.unvote'),
    'buttonClass' => 'text-sm font-semibold hover:underline',
    'withoutResignedVote' => false,
])

<div x-data="{
    voteDropdownOpen: false,
    delegate: '{{ $model->address() }}',
}">
    @if ($model->isResigned() && ! $withoutResignedVote)
        <div
            x-show="votingForAddress !== delegate"
            data-tippy-content="@lang('pages.wallet.delegate.resigned_vote_tooltip')"
        >
            <button
                type="button"
                href="javascript:void(0)"
                class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-dark-500"
                disabled
            >
                @lang('actions.vote')
            </button>
        </div>
    @endif

    <div
        @if ($model->isResigned())
            x-show="votingForAddress === delegate"
        @endif

        {{ $attributes }}
    >
        <x-general.dropdown.dropdown
            active-button-class=""
            dropdown-property="voteDropdownOpen"
            dropdown-background="bg-white dark:bg-theme-dark-900 dark:border dark:border-theme-dark-800"
            dropdown-class=""
            dropdown-padding=""
            button-wrapper-class=""
            button-class=""
            width="w-[147px]"
            content-class=""
        >
            <x-slot
                name="button"
                :class="Arr::toCssClasses([
                    'transition-default',
                    $buttonClass,
                ])"
                ::class="{
                    'text-theme-primary-600 hover:text-theme-primary-700 dark:text-theme-dark-blue-400 dark:hover:text-theme-dark-blue-500 dim:text-theme-dim-blue-600 dim:hover:text-theme-dim-blue-700': votingForAddress !== delegate,
                    'text-theme-danger-400 hover:text-theme-danger-500': votingForAddress === delegate,
                }"
            >
                <div x-show="votingForAddress !== delegate">
                    {{ $voteText }}
                </div>

                <div x-show="votingForAddress === delegate">
                    {{ $unvoteText }}
                </div>
            </x-slot>

            <div class="overflow-hidden rounded-t-xl">
                <div class="flex py-2 px-6 text-sm font-semibold bg-theme-secondary-200 leading-4.25 dark:bg-theme-dark-950">
                    @lang('general.vote_with')
                </div>

                <div class="flex flex-col py-3 px-6">
                    <x-ark-external-link
                        :url="$model->voteUrl()"
                        class="flex items-center py-3 space-x-2 font-semibold leading-5 link"
                        icon-class="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-secondary-500 dark:text-theme-dark-500"
                        :text="trans('brands.arkvault')"
                    />

                    <div
                        x-show="!isOnSameNetwork"
                        data-tippy-content="@lang('general.arkconnect.wrong_network')"
                    >
                        <button
                            type="button"
                            class="flex items-center py-3 space-x-2 font-semibold leading-5 text-theme-secondary-500 dark:text-theme-dark-500"
                            disabled
                            x-on:click="performVote('{{ $model->address() }}')"
                        >
                            @lang('brands.arkconnect')
                        </button>
                    </div>

                    <button
                        x-show="isOnSameNetwork"
                        type="button"
                        class="flex items-center py-3 space-x-2 font-semibold leading-5 link"
                        @click="performVote('{{ $model->address() }}')"
                    >
                        @lang('brands.arkconnect')
                    </button>
                </div>
            </div>
        </x-general.dropdown>
    </div>
</div>
