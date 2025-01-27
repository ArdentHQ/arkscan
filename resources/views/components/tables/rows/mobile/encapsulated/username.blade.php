@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :attributes="$attributes"
    :label="trans('labels.name')"
>
    @if ($model->hasUsername())
        <div class="inline-block text-theme-secondary-900 dark:text-theme-dark-50">
            {{ $model->username() }}
        </div>
    @else
        <div class="text-theme-secondary-500 dark:text-theme-dark-500">
            @lang('general.na')
        </div>
    @endif
</x-tables.rows.mobile.encapsulated.cell>
