@props(['argument'])

{{ ExplorerNumberFormatter::weiToArk((new ArgumentDecoder($argument))->decodeUnsignedInt()) }}
