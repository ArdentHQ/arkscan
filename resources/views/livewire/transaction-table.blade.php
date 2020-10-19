<div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Timestamp</th>
                <th>Type</th>
                <th>Sender</th>
                <th>Recipient</th>
                <th>Amount</th>
                <th>Fee</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id() }}</td>
                    <td>{{ $transaction->timestamp() }}</td>
                    <td>{{ $transaction->type() }}</td>
                    <td>{{ $transaction->sender() }}</td>
                    <td>{{ $transaction->recipient() }}</td>
                    <td>{{ $transaction->fee() }}</td>
                    <td>{{ $transaction->amount() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $transactions->links() }}
</div>
