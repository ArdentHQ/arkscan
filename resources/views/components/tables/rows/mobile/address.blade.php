<div class="flex justify-between w-full">
    @lang('labels.address')

    <x-general.address :address="$model->address()" with-loading />
</div>
