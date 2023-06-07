<div>
    <span class="font-semibold">
        @lang('labels.balance_percentage')
    </span>

    <span>
        <x-percentage>{{ $model->votePercentage() }}</x-percentage>
    </span>
</div>
