@props(['model'])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center') }}>
    <div class="text-sm font-semibold leading-[17px] dark:text-theme-secondary-500">
        @lang('labels.percentage')
    </div>

    <div class="text-theme-secondary-900 dark:text-theme-secondary-50 font-semibold inline-block">
        <x-percentage>{{ $model->votePercentage() }}</x-percentage>
    </div>
</div>
