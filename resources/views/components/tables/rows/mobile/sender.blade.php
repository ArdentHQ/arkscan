<div>
    @lang('labels.sender')

    <x-general.address :address="$model->sender()" with-loading />
</div>
