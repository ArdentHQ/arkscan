<div>
    @lang('labels.votes')

    {{ $model->votes() }}
    <span>{{ $model->votesPercentage() }}</span>
</div>
