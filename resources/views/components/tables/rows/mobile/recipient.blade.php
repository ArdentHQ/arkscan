<div>
    @lang('labels.recipient')

    <x-general.address :address="$model->recipient() ?? $model->sender()" with-loading />
</div>
