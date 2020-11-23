<div>
    @lang('labels.block_id')

    <a href="{{ $model->url() }}" class="font-semibold link">
        <x-truncate-middle>
            {{ $model->id() }}
        </x-truncate-middle>
    </a>
</div>
