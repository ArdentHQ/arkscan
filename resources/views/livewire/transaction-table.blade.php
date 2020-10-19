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
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->formatted_timestamp }}</td>
                    <td>{{ $transaction->type }} / {{ $transaction->type_group }}</td>
                    <td>{{ $transaction->sender->address }}</td>
                    <td>{{ $transaction->recipient->address }}</td>
                    <td>{{ $transaction->formatted_amount }}</td>
                    <td>{{ $transaction->formatted_fee }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $transactions->links() }}
</div>
