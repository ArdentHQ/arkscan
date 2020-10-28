<div class="flex justify-between w-full">
    @lang('labels.username')

    <x-general.address :address="$model->delegateUsername()" with-loading />
</div>
