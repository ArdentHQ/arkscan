<div class="w-full table-container md:hidden">
    <table>
        <thead>
            <tr>
                <x-tables.headers.mobile.number name="general.delegates.rank" alignment="text-left" />
                <x-tables.headers.mobile.text name="general.delegates.name" />
                <x-tables.headers.mobile.status name="general.delegates.status" alignment="text-right"/>
            </tr>
        </thead>
        <tbody>
            @foreach ($delegates as $delegate)
                <tr>
                    <td>
                        <x-tables.rows.mobile.rank :model="$delegate" />
                    </td>
                    <td>
                        <x-tables.rows.mobile.username-with-avatar :model="$delegate" />
                    </td>
                    <td>
                        <x-tables.rows.mobile.round-status-history :model="$delegate" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
