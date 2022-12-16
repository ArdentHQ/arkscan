@if ($model->isMigration())
    <x-details.address
        :title="trans('general.transaction.recipient')"
        :transaction="$model"
        :model="new App\Services\MigrationAddress"
    />
@else
    <x-details.address
        :title="trans('general.transaction.recipient')"
        :transaction="$model"
        :model="$model->recipient()"
    />
@endif
