@props(['model'])

<x-tables.rows.desktop.encapsulated.age
    :model="$model"
    :class="Arr::toCssClasses([
        'text-theme-secondary-700 dark:text-theme-dark-500',
        $attributes->get('class'),
    ])"
/>
