@props(['model'])

<div class="font-semibold !leading-4.25 text-sm text-theme-secondary-900 dark:text-theme-dark-50">
    @if ($model->hasForged())
        <div>
            @lang('tables.delegate-monitor.completed')
        </div>
    @elseif (! $model->isPending() && ! $model->hasForged() && ! $model->justMissed())
        <div>
            @lang('general.now')
        </div>
    @elseif ($model->justMissed())
        <div>
            @lang('tables.delegate-monitor.missed')
        </div>
    @else
        <x-delegates.time-to-forge
            :model="$model"
            class="text-sm font-semibold leading-4.25"
        />
    @endif
</div>
