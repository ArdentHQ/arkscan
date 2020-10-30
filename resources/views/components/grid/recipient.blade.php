<x-details.address
    :title="trans('general.transaction.recipient')"
    :transaction="$model"
    :model="$model->recipient()"
    icon="app-volume" />
