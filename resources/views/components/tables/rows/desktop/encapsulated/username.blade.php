@props(['model'])

@if ($model->username())
    <x-tables.rows.desktop.encapsulated.cell>
        {{ $model->username() }}
    </x-tables.rows.desktop.encapsulated.cell>
@endif
