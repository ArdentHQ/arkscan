@props(['model'])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center') }}>
    <div class="text-sm font-semibold leading-4.25 dark:text-theme-dark-500">
        @lang('labels.percentage')
    </div>

    <div class="inline-block font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
        <x-percentage>{{ $model->votePercentage() }}</x-percentage>
    </div>
</div>
