@if ($function)
Function: {{ $function }}

@endif
MethodID: 0x{{ $methodId }}
@foreach ($arguments as $index => $argument)
@lang('contracts.argument', [
    'index' => $index,
    'value' => $argument,
])
@endforeach
