@if (Network::usesMarketSquare())
    <a href="{{ $model->profileUrl() }}">
        <x-icon name="marketsquare" size="sm" style="secondary" />
    </a>
@endif
