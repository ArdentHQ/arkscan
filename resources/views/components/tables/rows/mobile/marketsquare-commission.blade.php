@if (Network::usesMarketSquare())
    <div>
        @lang('labels.marketsquare_commission')

        <x-percentage>{{ $model->commission() }}</x-percentage>
    </div>
@endif
