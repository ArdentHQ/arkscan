<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Facades\Wallets;
use App\Models\Composers\MultiPaymentAmountValueRangeComposer;
use App\Models\Composers\TimestampRangeComposer;
use App\Models\Composers\ValueRangeComposer;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

final class TransactionSearch implements Search
{
    public function search(array $parameters): Builder
    {
        $query = Transaction::query();

        $this->applyScopes($query, $parameters);

        if (! is_null(Arr::get($parameters, 'term'))) {
            $query->where('id', $parameters['term']);

            // Consider the term to be a wallet
            try {
                $query->orWhere(function ($query) use ($parameters): void {
                    $wallet = Wallets::findByIdentifier($parameters['term']);

                    $query->where(function ($query) use ($parameters, $wallet): void {
                        $query->where('sender_public_key', $wallet->public_key);

                        $this->applyScopes($query, $parameters);
                    });

                    $query->orWhere(function ($query) use ($parameters, $wallet): void {
                        $query->where('recipient_id', $wallet->address);

                        $this->applyScopes($query, $parameters);
                    });

                    $query->orWhere(function ($query) use ($parameters, $wallet): void {
                        $query->whereJsonContains('asset->payments', [['recipientId' => $wallet->address]]);

                        $this->applyScopes($query, $parameters);
                    });
                });
            } catch (\Throwable $th) {
                // If this throws then the term was not a valid address, public key or username.
            }

            // Consider the term to be a block
            $query->orWhere(function ($query) use ($parameters): void {
                $query->where(fn ($query): Builder => $query->where('block_id', $parameters['term']));

                if (is_numeric($parameters['term'])) {
                    $query->orWhere(fn ($query): Builder => $query->where('block_height', $parameters['term']));
                }
            });
        }

        return $query;
    }

    private function applyScopes(Builder $query, array $parameters): void
    {
        if (Arr::has($parameters, 'transactionType')) {
            if (Arr::get($parameters, 'transactionType') !== 'all') {
                $scopeClass = Transaction::TYPE_SCOPES[$parameters['transactionType']];

                /* @var \Illuminate\Database\Eloquent\Model */
                $query = $query->withScope($scopeClass);
            }
        }

        $query->where(function ($query) use ($parameters): void {
            ValueRangeComposer::compose($query, $parameters, 'amount');
            $query->orWhere(function ($query) use ($parameters): void {
                MultiPaymentAmountValueRangeComposer::compose($query, $parameters);
            });
        });

        ValueRangeComposer::compose($query, $parameters, 'fee');

        TimestampRangeComposer::compose($query, $parameters);

        if (! is_null(Arr::get($parameters, 'smartBridge'))) {
            $query->where('vendor_field', $parameters['smartBridge']);
        }
    }
}
