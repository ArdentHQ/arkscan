<x-details.address
    :title="trans('general.transaction.sender')"
    :transaction="$model"
    :model="$model->sender()"
/>
