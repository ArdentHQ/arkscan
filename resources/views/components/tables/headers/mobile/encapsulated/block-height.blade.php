@props(['model'])

<div {{ $attributes }}>
    <x-tables.rows.desktop.encapsulated.block-height :model="$model" />
</div>
