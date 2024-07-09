@props(['model'])

<div
    :wire:key="$model->publicKey().'-'.$model->forgingAt()->format('U')"
    class="font-semibold !leading-4.25 text-sm text-theme-secondary-900 dark:text-theme-dark-50"
>
    @if ($model->hasForged())
        <div>
            @lang('tables.validator-monitor.completed')
        </div>
    @elseif (! $model->isPending() && ! $model->hasForged() && ! $model->justMissed())
        <div>
            @lang('general.now')
        </div>
    @elseif ($model->justMissed())
        <div>
            @lang('tables.validator-monitor.missed')
        </div>
    @else
        <x-validators.time-to-forge
            :model="$model"
            class="text-sm font-semibold leading-4.25"
        />
    @endif
</div>
