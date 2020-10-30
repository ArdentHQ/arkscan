<x-currency>{{ $model->votes() }}</x-currency>

<span class="vote-percentage">
    <x-percentage>{{ $model->votesPercentage() }}</x-percentage>
</span>
