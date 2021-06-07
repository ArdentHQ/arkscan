<x-ark-tables.table sticky class="w-full pointer-events-none {{ $class }}">
    <thead>
        <tr>
            @foreach($headers as $name => $header)
                <x-dynamic-component
                    :component="$header['component']"
                    :name="$name"
                    :responsive="Arr::get($header, 'responsive', false)"
                    :breakpoint="Arr::get($header, 'breakpoint', 'lg')"
                    :first-on="Arr::get($header, 'firstOn', null)"
                    :last-on="Arr::get($header, 'lastOn', null)"
                />
            @endforeach
        </tr>
    </thead>
    <tbody>
        <x-skeleton>
            <x-ark-tables.row>
                @foreach($rows as $row)
                    <x-dynamic-component
                        :component="$row['component']"
                        :responsive="Arr::get($row, 'responsive', false)"
                        :breakpoint="Arr::get($row, 'breakpoint', 'lg')"
                        :first-on="Arr::get($row, 'firstOn', null)"
                        :last-on="Arr::get($row, 'lastOn', null)"
                    />
                @endforeach
            </x-ark-tables.row>
        </x-skeleton>
    </tbody>
</x-ark-tables.table>
