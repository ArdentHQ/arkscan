@if (Network::usesMarketSquare())
    <a href="{{ $model->profileUrl() }}">
        <x-ark-icon name="marketsquare" size="sm" style="secondary" />
    </a>
@endif
