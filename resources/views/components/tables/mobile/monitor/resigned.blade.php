<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($delegates as $delegate)
        <div class="space-y-3 table-list-mobile-row">
            <x-tables.rows.mobile.username-with-avatar :model="$delegate" />

            <x-tables.rows.mobile.votes :model="$delegate" />
        </div>
    @endforeach
</div>
