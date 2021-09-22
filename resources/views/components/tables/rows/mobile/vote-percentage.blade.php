<div>
    @lang('labels.balance_percentage')

    <span>
        <x-percentage>{{ $model->votePercentage() }}</x-percentage>
    </span>
</div>
