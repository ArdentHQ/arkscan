<div>
    <table>
        <thead>
            <tr>
                <th>Address</th>
                <th>Info</th>
                <th>Balance</th>
                <th>Supply</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wallets as $wallet)
                <tr>
                    <td>{{ $wallet->address }}</td>
                    <td>n/a</td>
                    <td>{{ $wallet->balance }}</td>
                    <td>n/a</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $wallets->links() }}
</div>
