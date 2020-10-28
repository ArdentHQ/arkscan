<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($delegates as $delegate)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <x-tables.rows.mobile.username-with-avatar :model="$delegate" />

            <x-tables.rows.mobile.votes :model="$delegate" />
        </div>
    @endforeach
</div>
