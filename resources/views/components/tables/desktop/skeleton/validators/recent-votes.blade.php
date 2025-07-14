@props([
    'rowCount' => 10,
    'paginator' => null,
    'isReady' => null,
])

@php
    if ($isReady === null) {
        $isReady = $this->isReady;
    }
@endphp

<x-table-skeleton
    device="desktop"
    :items="[
        'tables.recent-votes.id'         => [
            'type' => 'text',
            'nestedDataBreakpoint' => [
                'from' => 'md',
                'to' => 'xl',
            ],
        ],
        'tables.recent-votes.age'        => [
            'type'       => 'text',
            'responsive' => true,
            'breakpoint' => 'xl',
            'sortingId' => 'age',
            'livewireSort' => true,
            'isReady' => $isReady,
        ],
        'tables.recent-votes.addressing' => [
            'type' => 'encapsulated.addressing',
            'header' => 'address',
            'sortingId' => 'address',
            'livewireSort' => true,
            'isReady' => $isReady,
        ],
        'tables.recent-votes.type'       => [
            'type' => 'text',
            'sortingId' => 'type',
            'livewireSort' => true,
            'isReady' => $isReady,
        ],
        'tables.recent-votes.validator'   => [
            'type' => 'text',
            'sortingId' => 'name',
            'livewireSort' => true,
            'isReady' => $isReady,
        ],
    ]"
    :component-properties="['rounded' => false]"
    :row-count="$rowCount"
    :paginator="$paginator"
    encapsulated
/>
