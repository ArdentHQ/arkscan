@props([
    'model',
    'withoutLabel' => false,
    'identityClass' => null,
    'identityContentClass' => null,
    'identityLinkClass' => null,
])

<x-tables.rows.mobile.encapsulated.cell
    :label="$withoutLabel ? null : trans('tables.delegates.delegate')"
    :attributes="$attributes"
>
    <x-general.identity
        :model="$model"
        :class="$identityClass"
        :content-class="$identityContentClass"
        :link-class="$identityLinkClass"
        without-reverse
        without-icon
    />
</x-tables.rows.mobile.encapsulated.cell>
