@props([
    'id',
    'item',
    'key' => null,
    'translationKey' => null,
    'itemLangProperties' => null,
])

@if ($translationKey)
    @lang($translationKey.'.'.$key, $itemLangProperties)
@else
    @if (is_array($item))
        <span>{{ $item['text'] }}</span>

        <span class="text-theme-secondary-700 dark:text-theme-dark-500">(</span>

        <span>{{ $item['value'] }}</span>

        <span class="text-theme-secondary-700 dark:text-theme-dark-500">)</span>
    @else
        {{ $item }}
    @endif
@endif
