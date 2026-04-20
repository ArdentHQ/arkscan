@if ($function)
Function: {{ $function }}

@endif
MethodID: 0x{{ $methodId }}
@foreach ($arguments as $index => $argument)
[{{ $index }}]: {{ $argument }}

@endforeach
