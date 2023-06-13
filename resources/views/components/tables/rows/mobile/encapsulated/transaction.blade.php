@props([
    'model',
    'wallet' => null,
])

<div class="space-y-2 sm:flex sm:flex-col sm:justify-center">
    <div class="text-sm font-semibold dark:text-theme-secondary-500">
        @lang('general.transaction.types.'.$model->typeName())
    </div>

    <x-tables.rows.desktop.encapsulated.addressing
        :model="$model"
        :is-received="$wallet && $model->isReceived($wallet->address())"
    />
</div>
