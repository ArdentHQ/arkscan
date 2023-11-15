@php
    $paginatorIsEmpty = false;
    if ($paginator === null) {
        $paginatorIsEmpty = true;
    }

    if ($paginator !== null && $paginator->total() < config('arkscan.pagination.per_page')) {
        $paginatorIsEmpty = true;
    }

    if (isset($this) && property_exists($this, 'isReady') && ! $this->isReady) {
        $paginatorIsEmpty = true;
    }
@endphp

<x-tables.encapsulated-table
    :class="Arr::toCssClasses(['hidden w-full md:block', $class])"
    :rounded="$rounded ?? true"
    :with-pagination="! $paginatorIsEmpty"
    sticky
>
    <thead class="dark:bg-black bg-theme-secondary-100">
        <tr class="border-b-none">
            @foreach($headers as $name => $header)
                <x-dynamic-component
                    :component="$header['component']"
                    :name="$name"
                    :name-properties="Arr::get($header, 'nameProperties', null)"
                    :responsive="Arr::get($header, 'responsive', false)"
                    :breakpoint="Arr::get($header, 'breakpoint', 'lg')"
                    :first-on="Arr::get($header, 'firstOn', null)"
                    :last-on="Arr::get($header, 'lastOn', null)"
                    :class="Arr::get($header, 'class', null)"
                    :width="Arr::get($header, 'width', null)"
                    :sorting-id="Arr::get($header, 'sortingId', null)"
                    :livewire-sort="Arr::get($header, 'livewireSort', null)"
                    :sort-disabled="Arr::get($header, 'sortDisabled', false)"
                    :tooltip="Arr::get($header, 'tooltip', null)"
                />
            @endforeach
        </tr>
    </thead>
    <tbody>
        <x-skeleton :row-count="$rowCount">
            <x-ark-tables.row>
                @foreach($rows as $row)
                    <x-dynamic-component
                        :component="$row['component']"
                        :responsive="Arr::get($row, 'responsive', false)"
                        :breakpoint="Arr::get($row, 'breakpoint', 'lg')"
                        :first-on="Arr::get($row, 'firstOn', null)"
                        :last-on="Arr::get($row, 'lastOn', null)"
                        :generic="Arr::get($row, 'generic', null)"
                        :class="Arr::get($row, 'class', null)"
                        :nested-data-breakpoint="Arr::get($row, 'nestedDataBreakpoint', null)"
                        :badge-width="Arr::get($row, 'badgeWidth', null)"
                        :badge-height="Arr::get($row, 'badgeHeight', null)"
                    />
                @endforeach
            </x-ark-tables.row>
        </x-skeleton>
    </tbody>
</x-ark-tables.encapsulated-table>
