@props(['argument'])

{{ (new ArgumentDecoder($argument))->decodeAddress() }}
