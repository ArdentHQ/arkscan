@props([
    'model',
    'width' => 'min-w-[8.75rem]',
    'withTime' => false,
    'withText' => true,
])

<x-general.badge
    :attributes="$attributes"
    :colors="Arr::toCssClasses([
        'inline-flex space-x-2 items-center whitespace-nowrap',
        '!px-2' => $withText,
        'border-transparent bg-theme-secondary-200 dark:border-theme-dark-700 encapsulated-badge' => $withText && $model->isPending(),
        'border-transparent bg-theme-success-100 dark:border-theme-success-700' => $withText && $model->hasForged(),
        'border-transparent bg-theme-danger-100 dark:border-theme-danger-400' => $withText && $model->justMissed(),
        'border-transparent bg-theme-primary-100 dark:border-theme-dark-blue-600 dim:!border-theme-dark-blue-800' => $withText && ! $model->isPending() && ! $model->hasForged() && ! $model->justMissed(),
        'border-none' => ! $withText,
        $width => $withText,
    ])"
>
    <div class="flex items-center">
        <div @class([
            'w-3 h-3 rounded-full',
            'bg-theme-secondary-500 dark:bg-theme-dark-500' => $model->isPending(),
            'bg-theme-success-700 dark:bg-theme-success-500' => $model->hasForged(),
            'bg-theme-danger-600 dark:bg-theme-danger-300' => $model->justMissed(),
            'bg-theme-primary-600 dark:bg-theme-dark-blue-400 dim:!bg-theme-dark-blue-600' => ! $model->isPending() && ! $model->hasForged() && ! $model->justMissed(),
        ])></div>
    </div>

    @if ($withText)
        <div @class([
            'leading-3.75',
            'text-theme-secondary-700 dark:text-theme-dark-200' => $model->isPending(),
            'text-theme-success-700 dark:text-theme-success-500' => $model->hasForged(),
            'text-theme-danger-600 dark:text-theme-danger-300' => $model->justMissed(),
            'text-theme-primary-600 dark:text-theme-dark-blue-400 dim:!text-theme-dark-blue-600' => ! $model->isPending() && ! $model->hasForged() && ! $model->justMissed(),
        ])>
            @if ($model->isPending())
                @if ($withTime)
                    <x-delegates.time-to-forge
                        :model="$model"
                        class="text-xs font-semibold leading-3.75"
                    />
                @else
                    @lang('tables.delegate-monitor.forging-status.pending')
                @endif
            @elseif ($model->hasForged())
                @lang('tables.delegate-monitor.forging-status.block_generated')
            @elseif ($model->justMissed())
                @lang('tables.delegate-monitor.forging-status.blocks_missed', ['count' => $model->missedCount()])
            @else
                @lang('tables.delegate-monitor.forging-status.generating')
            @endif
        </div>
    @endif
</x-general.badge>
