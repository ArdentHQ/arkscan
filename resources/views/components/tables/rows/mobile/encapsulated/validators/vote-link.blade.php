@props(['model'])

@if (! $model->isResigned())
    <span {{ $attributes }}>
        <x-tables.rows.desktop.encapsulated.validators.vote-link :model="$model" />
    </span>
@endif
