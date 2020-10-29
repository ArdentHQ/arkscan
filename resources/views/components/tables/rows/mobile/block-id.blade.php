<div>
    @lang('labels.block_id')

    <a href="{{ $model->url() }}" class="font-semibold link">
        <x-truncate-middle :value="$model->id()" />
    </a>
</div>
