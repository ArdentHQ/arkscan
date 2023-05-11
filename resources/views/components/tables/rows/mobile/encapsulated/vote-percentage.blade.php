<div>
    <span class="font-semibold">
        @lang('labels.percentage')
    </span>

    <span class="font-semibold">
        <x-percentage>{{ $model->votePercentage() }}</x-percentage>
    </span>
</div>
