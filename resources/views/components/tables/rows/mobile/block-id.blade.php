<div>
    <span class="font-semibold">
        @lang('labels.block_id')
    </span>

    <a href="{{ $model->url() }}" class="font-semibold link">
        <x-truncate-middle>
            {{ $model->id() }}
        </x-truncate-middle>
    </a>
</div>
