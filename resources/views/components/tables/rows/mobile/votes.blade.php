<div>
    @lang('labels.votes')

    {{ $model->votes() }}
    <span><x-percentage>{{ $model->votesPercentage() }}</x-percentage></span>
</div>
