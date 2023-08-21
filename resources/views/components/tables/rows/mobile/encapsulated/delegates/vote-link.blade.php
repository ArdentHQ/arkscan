@props(['model'])

@if (! $model->isResigned())
    <span {{ $attributes }}>
        <x-tables.rows.desktop.encapsulated.delegates.vote-link :model="$model" />
    </span>
@endif
