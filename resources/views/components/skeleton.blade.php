@props([
    'rowCount' => 15,
])

@for ($i = 0; $i < $rowCount; $i++)
    {{ $slot }}
@endfor
