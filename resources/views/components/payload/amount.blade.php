@props([
    'argument',
    'suffix',
])

{{ ExplorerNumberFormatter::weiToArk((new ArgumentDecoder($argument))->decodeUnsignedInt(), false) }} {{ $suffix }}
