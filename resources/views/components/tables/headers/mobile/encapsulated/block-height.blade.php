@props([
    'model',
    'withoutLink' => false,
])

<x-tables.rows.desktop.encapsulated.block-height
    :model="$model"
    :without-link="$withoutLink"
    :attributes="$attributes"
/>
