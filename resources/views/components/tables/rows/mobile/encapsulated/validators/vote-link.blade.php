@props(['model'])

@if (config('arkscan.arkconnect.enabled', false))
    <x-validators.vote-link
        :model="$model"
        :attributes="$attributes"
        without-resigned-vote
    />
@elseif (! $model->isResigned())
    <span {{ $attributes }}>
        <x-tables.rows.desktop.encapsulated.validators.vote-link :model="$model" />
    </span>
@endif
