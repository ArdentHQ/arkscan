@props(['model'])

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
            class="text-sm font-semibold transition-default hover:underline"
            ::class="{
                'text-theme-primary-600 hover:text-theme-primary-700 dark:text-theme-dark-blue-400 dark:hover:text-theme-dark-blue-500': votingFor !== delegate,
                'text-theme-danger-400 hover:text-theme-danger-500': votingFor === delegate,
            }"
        >
            <div x-show="votingFor !== delegate">
                @lang('actions.vote')
            </div>

            <div x-show="votingFor === delegate">
                @lang('actions.unvote')
            </div>
        </x-slot>

        <div class="rounded-t-xl overflow-hidden">
            <div class="flex bg-theme-secondary-200 dark:bg-theme-dark-950 leading-4.25 text-sm py-2 px-6 font-semibold">
                @lang('general.vote_with')
            </div>

            <div class="flex flex-col py-3 px-6">
                <x-ark-external-link
                    :url="$model->voteUrl()"
                    class="flex items-center space-x-2 font-semibold leading-5 py-3 link"
                    icon-class="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-secondary-500 dark:text-theme-dark-500"
                    :text="trans('brands.arkvault')"
                />

                <button
                    type="button"
                    class="flex items-center space-x-2 font-semibold leading-5 py-3 link"
                    @click="performVote('{{ $model->address() }}')"
                >
                    @lang('brands.arkconnect')
                </button>
            </div>
        </div>
    </x-general.dropdown>
</div>
