@props(['model'])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center leading-4.25') }}>
    <div class="text-sm font-semibold leading-4.25 dark:text-theme-dark-500">
        @lang('tables.blocks.total_reward', ['currency' => Network::currency()])
    </div>

    <div class="inline-block font-semibold text-theme-secondary-900 leading-4.25 dark:text-theme-dark-50">
        <x-tables.rows.desktop.encapsulated.reward :model="$model" />
    </div>
</div>
