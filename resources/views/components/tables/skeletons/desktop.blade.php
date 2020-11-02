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
                <tr>
                    @foreach($rows as $row)
                        <x-dynamic-component :component="$row" />
                    @endforeach
                </tr>
            </x-skeleton>
        </tbody>
    </table>
</div>
