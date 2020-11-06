<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Facades\Wallets;
use App\Models\Composers\MultiPaymentAmountValueRangeComposer;
use App\Models\Composers\TimestampRangeComposer;
use App\Models\Composers\ValueRangeComposer;
use App\Models\Scopes\BusinessEntityRegistrationScope;
use App\Models\Scopes\BusinessEntityResignationScope;
use App\Models\Scopes\BusinessEntityUpdateScope;
use App\Models\Scopes\DelegateEntityRegistrationScope;
use App\Models\Scopes\DelegateEntityResignationScope;
use App\Models\Scopes\DelegateEntityUpdateScope;
use App\Models\Scopes\DelegateRegistrationScope;
use App\Models\Scopes\DelegateResignationScope;
use App\Models\Scopes\EntityRegistrationScope;
use App\Models\Scopes\EntityResignationScope;
use App\Models\Scopes\EntityUpdateScope;
use App\Models\Scopes\IpfsScope;
use App\Models\Scopes\LegacyBridgechainRegistrationScope;
use App\Models\Scopes\LegacyBridgechainResignationScope;
use App\Models\Scopes\LegacyBridgechainUpdateScope;
use App\Models\Scopes\LegacyBusinessRegistrationScope;
use App\Models\Scopes\LegacyBusinessResignationScope;
use App\Models\Scopes\LegacyBusinessUpdateScope;
use App\Models\Scopes\ModuleEntityRegistrationScope;
use App\Models\Scopes\ModuleEntityResignationScope;
use App\Models\Scopes\ModuleEntityUpdateScope;
use App\Models\Scopes\MultiPaymentScope;
use App\Models\Scopes\MultiSignatureScope;
use App\Models\Scopes\PluginEntityRegistrationScope;
use App\Models\Scopes\PluginEntityResignationScope;
use App\Models\Scopes\PluginEntityUpdateScope;
use App\Models\Scopes\ProductEntityRegistrationScope;
use App\Models\Scopes\ProductEntityResignationScope;
use App\Models\Scopes\ProductEntityUpdateScope;
use App\Models\Scopes\SecondSignatureScope;
use App\Models\Scopes\TimelockClaimScope;
use App\Models\Scopes\TimelockRefundScope;
use App\Models\Scopes\TimelockScope;
use App\Models\Scopes\TransferScope;
use App\Models\Scopes\VoteCombinationScope;
use App\Models\Scopes\VoteScope;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

final class TransactionSearch implements Search
{
    private array $scopes = [
        'businessEntityRegistration'    => BusinessEntityRegistrationScope::class,
        'businessEntityResignation'     => BusinessEntityResignationScope::class,
        'businessEntityUpdate'          => BusinessEntityUpdateScope::class,
        'delegateEntityRegistration'    => DelegateEntityRegistrationScope::class,
        'delegateEntityResignation'     => DelegateEntityResignationScope::class,
        'delegateEntityUpdate'          => DelegateEntityUpdateScope::class,
        'delegateRegistration'          => DelegateRegistrationScope::class,
        'delegateResignation'           => DelegateResignationScope::class,
        'entityRegistration'            => EntityRegistrationScope::class,
        'entityResignation'             => EntityResignationScope::class,
        'entityUpdate'                  => EntityUpdateScope::class,
        'ipfs'                          => IpfsScope::class,
        'legacyBridgechainRegistration' => LegacyBridgechainRegistrationScope::class,
        'legacyBridgechainResignation'  => LegacyBridgechainResignationScope::class,
        'legacyBridgechainUpdate'       => LegacyBridgechainUpdateScope::class,
        'legacyBusinessRegistration'    => LegacyBusinessRegistrationScope::class,
        'legacyBusinessResignation'     => LegacyBusinessResignationScope::class,
        'legacyBusinessUpdate'          => LegacyBusinessUpdateScope::class,
        'moduleEntityRegistration'      => ModuleEntityRegistrationScope::class,
        'moduleEntityResignation'       => ModuleEntityResignationScope::class,
        'moduleEntityUpdate'            => ModuleEntityUpdateScope::class,
        'multiPayment'                  => MultiPaymentScope::class,
        'multiSignature'                => MultiSignatureScope::class,
        'pluginEntityRegistration'      => PluginEntityRegistrationScope::class,
        'pluginEntityResignation'       => PluginEntityResignationScope::class,
        'pluginEntityUpdate'            => PluginEntityUpdateScope::class,
        'productEntityRegistration'     => ProductEntityRegistrationScope::class,
        'productEntityResignation'      => ProductEntityResignationScope::class,
        'productEntityUpdate'           => ProductEntityUpdateScope::class,
        'secondSignature'               => SecondSignatureScope::class,
        'timelockClaim'                 => TimelockClaimScope::class,
        'timelockRefund'                => TimelockRefundScope::class,
        'timelock'                      => TimelockScope::class,
        'transfer'                      => TransferScope::class,
        'vote'                          => VoteScope::class,
        'voteCombination'               => VoteCombinationScope::class,
    ];

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

                if (is_int($parameters['term'])) {
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
                $scopeClass = $this->scopes[$parameters['transactionType']];

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
