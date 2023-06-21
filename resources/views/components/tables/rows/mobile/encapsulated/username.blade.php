@props(['model'])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center font-semibold') }}>
    <div class="text-sm leading-[17px] dark:text-theme-secondary-500">
        @lang('labels.name')
    </div>

    @if ($model->username())
        <div class="inline-block text-theme-secondary-900 dark:text-theme-secondary-50">
            {{ $model->username() }}
        </div>
    @else
        <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
            @lang('general.na')
        </div>
    @endif
</div>
