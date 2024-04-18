@props(['model'])

<div
    x-show="votingFor === '{{ $model->publicKey() }}'"
    class="flex items-center space-x-2 bg-theme-secondary-200 dark:bg-theme-dark-800 dark:text-theme-dark-200 p-3 -mx-3 mb-1"
>
    <div>
        <x-ark-icon
            name="check-mark-box"
            size="sm"
        />
    </div>

    <div class="font-semibold">
        @lang('pages.delegates.voting_for_tooltip')
    </div>
</div>
