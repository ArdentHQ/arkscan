@props([
    'model',
    'withoutAge' => false,
])

<div>
    <x-tables.rows.desktop.encapsulated.transaction-id
        :model="$model"
        :without-age="$withoutAge"
    />
</div>
