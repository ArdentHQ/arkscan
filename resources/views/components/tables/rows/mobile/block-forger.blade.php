<div class="flex justify-between w-full">
    @lang('labels.block_forger')

    <x-general.address :address="$model->delegateUsername()" with-loading />
</div>
