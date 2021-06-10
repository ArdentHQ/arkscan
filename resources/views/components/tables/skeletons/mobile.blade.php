<div class="divide-y table-list-mobile">
    <x-skeleton>
        <div class="table-list-mobile-row">
            @foreach($rows as $row)
                <x-dynamic-component :component="$row['component']" />
            @endforeach
        </div>
    </x-skeleton>
</div>
