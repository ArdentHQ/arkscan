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
                    <td>{{ $transaction->timestamp }}</td>
                    <td>{{ $transaction->type }} / {{ $transaction->type_group }}</td>
                    <td>{{ $transaction->sender_public_key }}</td>
                    <td>{{ $transaction->recipient_id }}</td>
                    <td>{{ $transaction->amount }}</td>
                    <td>{{ $transaction->fee }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $transactions->links() }}
</div>
