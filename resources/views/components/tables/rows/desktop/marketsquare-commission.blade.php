@if (Network::usesMarketSquare())
    <x-percentage>{{ $model->commission() }}</x-percentage>
@endif
