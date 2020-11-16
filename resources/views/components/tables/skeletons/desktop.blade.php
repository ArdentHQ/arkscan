<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                @foreach($headers as $name => $header)
                    <x-dynamic-component :component="$header" :name="$name" />
                @endforeach
            </tr>
        </thead>
        <tbody>
            <x-skeleton>
                <x-ark-tables.row>
                    @foreach($rows as $row)
                        <x-dynamic-component :component="$row" />
                    @endforeach
                </x-ark-tables.row>
            </x-skeleton>
        </tbody>
    </table>
</div>
