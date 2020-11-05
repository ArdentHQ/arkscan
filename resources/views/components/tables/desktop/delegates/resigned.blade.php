<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <x-tables.headers.desktop.text name="general.delegates.id" />
                <x-tables.headers.desktop.address name="general.delegates.name" />
                <x-tables.headers.desktop.number name="general.delegates.votes" responsive/>
            </tr>
        </thead>
        <tbody>
            @foreach($delegates as $delegate)
                <tr>
                    <td>
                        <x-tables.rows.desktop.resignation-id :model="$delegate" />
                    </td>
                    <td>
                        <x-tables.rows.desktop.username :model="$delegate" />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.votes :model="$delegate" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
