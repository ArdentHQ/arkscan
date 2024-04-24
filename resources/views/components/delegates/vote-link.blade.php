@props([
    'model',
    'voteText' => trans('actions.vote'),
    'unvoteText' => trans('actions.unvote'),
    'buttonClass' => 'text-sm font-semibold hover:underline',
])

<div x-data="{
    voteDropdownOpen: false,
    delegate: '{{ $model->address() }}',
}">
    <x-general.dropdown.dropdown
        active-button-class="space-x-1.5"
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
                'text-theme-primary-600 hover:text-theme-primary-700 dark:text-theme-dark-blue-400 dark:hover:text-theme-dark-blue-500 dim:text-theme-dim-blue-600 dim:hover:text-theme-dim-blue-700': votingFor !== delegate,
                'text-theme-danger-400 hover:text-theme-danger-500': votingFor === delegate,
            }"
        >
            <div x-show="votingFor !== delegate">
                {{ $voteText }}
            </div>

            <div x-show="votingFor === delegate">
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

                <button
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
