<span>
    <span>{{ $value }}</span>

    @if (ExplorerNumberFormatter::hasSymbol(Settings::currency()))
        <span>{{ Settings::currency() }}</span>
    @endif
</span>
