@props(['model'])

<div
    x-show="votingForAddress === '{{ $model->address() }}'"
    class="flex items-center p-3 -mx-3 mb-1 space-x-2 bg-theme-secondary-200 dark:bg-theme-dark-800 dark:text-theme-dark-200"
>
    <div>
        <x-ark-icon
            name="check-mark-box"
            size="sm"
        />
    </div>

    <div class="font-semibold">
        @lang('pages.delegates.arkconnect.voting_for_tooltip')
    </div>
</div>
