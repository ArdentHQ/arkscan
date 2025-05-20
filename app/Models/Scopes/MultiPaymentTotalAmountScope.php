<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\ContractMethod;
use App\Facades\Network;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

final class MultiPaymentTotalAmountScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Ignore next line as the `joinSubLateral` works as intended since it is a macro method.
        // @phpstan-ignore-next-line
        $builder->joinSubLateral(function ($query) {
            $query->select('recipient_amount', 'is_multipayment')
                ->from(function ($query) {
                    $query->selectRaw('(\'0x\' || encode(substring(transactions.data, 69, 32), \'hex\'))::numeric::int as tx_count')
                        ->selectRaw(
                            'CASE WHEN transactions.to = ? AND encode(SUBSTRING(data FROM 1 FOR 4), \'hex\') = ? THEN
                                TRUE
                            ELSE
                                FALSE
                            END as is_multipayment',
                            [Network::knownContract('multipayment'), ContractMethod::multiPayment()]
                        )
                        ->from('receipts')
                        ->where('receipts.transaction_hash', '=', DB::raw('transactions.hash'))
                        ->where('receipts.status', 1);
                }, 'd')
                ->joinSubLateral(function ($query) {
                    $query->selectRaw(
                        'SUM(
                            CASE WHEN is_multipayment THEN
                                (\'0x\' || encode(substring(transactions.data, 69 + ((tx_count + 1) * 32) + (n * 32), 32), \'hex\'))::float::numeric
                            ELSE
                                0
                            END
                        ) as recipient_amount'
                    )
                    ->from(DB::raw(
                        'generate_series(
                            1,
                            CASE WHEN is_multipayment THEN
                                (\'0x\' || encode(substring(transactions.data, 69 + ((tx_count + 1) * 32), 32), \'hex\'))::float::numeric::int
                            ELSE
                                0
                            END
                        ) n'
                    ));
                }, 'recipient_amounts_nested', 'is_multipayment', '=', DB::raw('true'), 'left outer');
        }, 'recipient_amounts', 'is_multipayment', '=', DB::raw('true'), 'left outer');
    }
}
