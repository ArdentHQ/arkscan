@if ($model->isMigration())
    <x-details.migration-address
        :title="trans('general.transaction.recipient')"
        :transaction="$model"
        :model="$model->recipient()"
    />
@else
    <x-details.address
        :title="trans('general.transaction.recipient')"
        :transaction="$model"
        :model="$model->recipient()"
    />
@endif
