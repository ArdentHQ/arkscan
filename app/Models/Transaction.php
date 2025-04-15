<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Models\Casts\UnixSeconds;
use App\Models\Concerns\HasEmptyScope;
use App\Models\Concerns\SearchesCaseInsensitive;
use App\Models\Concerns\Transaction\CanBeSorted;
use App\Models\Scopes\ContractDeploymentScope;
use App\Models\Scopes\MultiPaymentScope;
use App\Models\Scopes\OtherTransactionTypesScope;
use App\Models\Scopes\TransferScope;
use App\Models\Scopes\UnvoteScope;
use App\Models\Scopes\UsernameRegistrationScope;
use App\Models\Scopes\UsernameResignationScope;
use App\Models\Scopes\ValidatorRegistrationScope;
use App\Models\Scopes\ValidatorResignationScope;
use App\Models\Scopes\VoteScope;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

/**
 * @property string $id
 * @property BigNumber $value
 * @property BigNumber $gas_limit
 * @property BigNumber $gas_price
 * @property int $timestamp
 * @property int $transaction_index

// block has hash, block hash in in the table * @property string $block_hash
 * @property string|null $to
 * @property string $from
 * @property string $sender_public_key
 * @property int $block_number
 * @property resource|null $data
 * @property int $nonce
 * @property Wallet $sender
 * @method static \Illuminate\Database\Eloquent\Builder withScope(string $scope)
 */
final class Transaction extends Model
{
    use CanBeSorted;
    use HasFactory;
    use SearchesCaseInsensitive;
    use HasEmptyScope;
    use Searchable;

    /**
     * A list of transaction scopes used for filtering based on type.
     *
     * Exposed through the model to keep its usage consistent across
     * all places that need to filter transactions by their type.
     */
    public const TYPE_SCOPES = [
        'validatorRegistration' => ValidatorRegistrationScope::class,
        'validatorResignation'  => ValidatorResignationScope::class,
        'transfer'              => TransferScope::class,
        'multiPayment'          => MultiPaymentScope::class,
        'vote'                  => VoteScope::class,
        'unvote'                => UnvoteScope::class,
    ];

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    public $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['serialized'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value'                 => BigInteger::class,
        'gas_price'             => BigInteger::class,
        'gas_limit'             => BigInteger::class,
        'timestamp'             => UnixSeconds::class,
        'transaction_index'     => 'int',
        'block_number'          => 'int',
    ];

    protected $with = [
        'receipt',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        // Notice that we only need to index the data used on to hydrate the model
        // for the search results.
        return [
            // Searchable id and used to link the transaction
            'hash' => $this->hash,
            // Used to get the recipient wallet
            'to' => $this->to,

            // Used to get the sender wallets
            'sender_public_key' => $this->sender_public_key,

            // To get the value for single payments
            // Using `__toString` since are instances of `BigNumber`
            'value'  => $this->value->__toString(),
            'fee'    => $this->gas_price->__toString(),
            // used to build the payments and sortable
            'timestamp' => $this->timestamp,
        ];
    }

    /**
     * @return Builder<self>
     */
    public static function getSearchableQuery(): Builder
    {
        $self = new static();

        return $self->newQuery()
            ->select([
                'id',
                'sender_public_key',
                'to',
                'value',
                'gas_price',
                'timestamp',
            ])
            ->when(true, function ($query) use ($self) {
                $self->makeAllSearchableUsing($query);
            });
    }

    /**
     * Overrides `vendor/laravel/scout/src/Searchable.php@makeAllSearchable`
     * to optimize the query.
     *
     * @param  int  $chunk
     * @return void
     */
    public static function makeAllSearchable($chunk = null)
    {
        $self = new static();

        // @phpstan-ignore-next-line
        $self::getSearchableQuery()->searchable($chunk);
    }

    /**
     * A transaction belongs to a block.
     *
     * @return BelongsTo
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_hash', 'hash');
    }

    /**
     * A transaction belongs to a sender.
     *
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'sender_public_key', 'public_key');
    }

    /**
     * A receipt belongs to a transaction.
     *
     * @return HasOne
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class, 'transaction_hash', 'hash');
    }

    public function scopeWithTypeFilter(Builder $query, array $filter): Builder
    {
        $hasAdjustedFilters = in_array(false, $filter, true);

        return $query
            ->when($hasAdjustedFilters, function ($query) use ($filter) {
                $query->where(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->when($filter['transfers'] === true, function ($query) {
                            $query->withScope(TransferScope::class);
                        });
                    })
                    ->orWhere(function ($query) use ($filter) {
                        $query->when($filter['multipayments'] === true, function ($query) {
                            $query->withScope(MultiPaymentScope::class);
                        });
                    })
                    ->orWhere(function ($query) use ($filter) {
                        $query->when($filter['votes'] === true, function ($query) {
                            $query->withScope(VoteScope::class);
                        });
                    })
                    ->orWhere(function ($query) use ($filter) {
                        $query->when($filter['votes'] === true, function ($query) {
                            $query->withScope(UnvoteScope::class);
                        });
                    })
                    ->orWhere(function ($query) use ($filter) {
                        $query->when($filter['validator'] === true, function ($query) {
                            $query->withScope(ValidatorRegistrationScope::class);
                        });
                    })
                    ->orWhere(function ($query) use ($filter) {
                        $query->when($filter['validator'] === true, function ($query) {
                            $query->withScope(ValidatorResignationScope::class);
                        });
                    })
                    ->orWhere(function ($query) use ($filter) {
                        $query->when($filter['username'] === true, function ($query) {
                            $query->withScope(UsernameRegistrationScope::class);
                        });
                    })
                    ->orWhere(function ($query) use ($filter) {
                        $query->when($filter['username'] === true, function ($query) {
                            $query->withScope(UsernameResignationScope::class);
                        });
                    })
                    ->orWhere(function ($query) use ($filter) {
                        $query->when($filter['contract_deployment'] === true, function ($query) {
                            $query->withScope(ContractDeploymentScope::class);
                        });
                    })
                    ->orWhere(function ($query) use ($filter) {
                        $query->when($filter['others'] === true, function ($query) {
                            $query->withScope(OtherTransactionTypesScope::class);
                        });
                    });
                });
            });
    }

    public function fee(): BigNumber
    {
        $gasPrice = clone $this->gas_price;
        if ($this->receipt === null) {
            return $gasPrice;
        }

        return $gasPrice->multipliedBy($this->receipt->gas_used->valueOf());
    }

    /**
     * Get the current connection name for the model.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return 'explorer';
    }
}
