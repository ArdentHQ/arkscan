<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Facades\Wallets;
use App\Models\Composers\MultiPaymentAmountValueRangeComposer;
use App\Models\Composers\TimestampRangeComposer;
use App\Models\Composers\ValueRangeComposer;
use App\Models\Transaction;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Throwable;

final class TransactionSearch implements Search
{
    use ValidatesTerm;

    public function search(array $parameters): Builder
    {
        $query = Transaction::query();

        $this->applyScopes($query, $parameters);

        $term = Arr::get($parameters, 'term');

        if (! is_null($term)) {
            if ($this->couldBeTransactionID($term)) {
                $query->whereLower('id', $term);
            } else {
                $query->empty();
            }

            // Consider the term to be a wallet
            try {
                $query->orWhere(function ($query) use ($parameters, $term): void {
                    $wallet = Wallets::findByIdentifier($term);

                    $query->where(function ($query) use ($parameters, $wallet): void {
                        $query->whereLower('sender_public_key', $wallet->public_key);

                        $this->applyScopes($query, $parameters);
                    });

                    $query->orWhere(function ($query) use ($parameters, $wallet): void {
                        $query->whereLower('recipient_id', $wallet->address);

                        $this->applyScopes($query, $parameters);
                    });

                    $query->orWhere(function ($query) use ($parameters, $wallet): void {
                        $query->whereJsonContains('asset->payments', [['recipientId' => $wallet->address]]);

                        $this->applyScopes($query, $parameters);
                    });
                });
            } catch (Throwable) {
                // If this throws then the term was not a valid address, public key or username.
            }

            if ($this->couldBeBlockID($term) || $this->couldBeHeightValue($term)) {
                // Consider the term to be a block
                $query->orWhere(function ($query) use ($term): void {
                    if ($this->couldBeBlockID($term)) {
                        $query->where(fn ($query): Builder => $query->whereLower('block_id', $term));
                    }

                    if ($this->couldBeHeightValue($term)) {
                        $numericTerm = strval(filter_var($term, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND));
                        $query->orWhere(fn ($query): Builder => $query->where('block_height', $numericTerm));
                    }
                });
            }
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
