@props(['model'])

<div class="text-sm font-semibold leading-[17px] text-theme-secondary-900 dark:text-theme-secondary-200">
    <x-percentage>{{ $model->votePercentage() }}</x-percentage>
</div>
