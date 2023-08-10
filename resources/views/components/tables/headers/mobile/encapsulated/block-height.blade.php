@props([
    'model',
    'withoutLink' => false,
])

<div {{ $attributes }}>
    <x-tables.rows.desktop.encapsulated.block-height
        :model="$model"
        :without-link="$withoutLink"
    />
</div>
