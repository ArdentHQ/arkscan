<div class="divide-y table-list-mobile table-list-encapsulated">
    @foreach ($exchanges as $exchange)
        <div class="table-list-mobile-row">
            <div>
                {{ $exchange['name'] }}
            </div>

            <div>
                {{ implode(', ', $exchange['pairs']) }}
            </div>

            <div>
                {{ $exchange['price'] }}
            </div>

            <div>
                {{ $exchange['volume'] }}
            </div>
        </div>
    @endforeach
</div>
