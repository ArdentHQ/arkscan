@props(['model'])

@if (config('arkscan.arkconnect.enabled', false))
    <x-delegates.arkconnect.vote-link
        :model="$model"
        :attributes="$attributes"
        without-resigned-vote
    />
@elseif (! $model->isResigned())
    <span {{ $attributes }}>
        <x-tables.rows.desktop.encapsulated.delegates.vote-link :model="$model" />
    </span>
@endif
