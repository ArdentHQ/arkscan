<x-general.entity-header-item
    :title="trans('pages.transaction.transaction_type')"
    icon="app-transactions.{{ $model->iconType() }}"
    :wrapper-class="$wrapperClass ?? ''"
>
    <x-slot name="text">
        @isset($asEntity)
            @lang('general.transaction.types.'.$model->entityType())
        @else
            {{ $model->typeLabel() }}
        @endisset
    </x-slot>
</x-general.entity-header-item>
