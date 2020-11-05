<div class="w-full table-container md:hidden">
    <table>
        <thead>
            <tr>
                <x-tables.headers.mobile.number name="general.delegates.rank" />
                <x-tables.headers.mobile.text name="general.delegates.name" />
                <x-tables.headers.mobile.number name="general.delegates.votes" alignment="sm:text-left" />
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
                    <x-tables.rows.mobile.votes :model="$delegate" />
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>