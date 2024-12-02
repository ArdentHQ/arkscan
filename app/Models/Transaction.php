<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Models\Casts\UnixSeconds;
use App\Models\Concerns\HasEmptyScope;
use App\Models\Concerns\SearchesCaseInsensitive;
use App\Models\Concerns\Transaction\CanBeSorted;
use App\Models\Scopes\MultiPaymentScope;
use App\Models\Scopes\OtherTransactionTypesScope;
use App\Models\Scopes\TransferScope;
use App\Models\Scopes\UnvoteScope;
use App\Models\Scopes\ValidatorRegistrationScope;
use App\Models\Scopes\ValidatorResignationScope;
use App\Models\Scopes\VoteScope;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Laravel\Scout\Searchable;

/**
 * @property string $id
 * @property BigNumber $amount
 * @property BigNumber $gas_limit
 * @property BigNumber $gas_price
 * @property int $timestamp
 * @property int $sequence
 * @property string $block_id
 * @property string|null $recipient_address
 * @property string $sender_public_key
 * @property int $block_height
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
        'amount'       => BigInteger::class,
        'gas_price'    => BigInteger::class,
        'gas_limit'    => BigInteger::class,
        'timestamp'    => UnixSeconds::class,
        'sequence'     => 'int',
        'block_height' => 'int',
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
            'id' => $this->id,
            // Used to get the recipient wallet
            'recipient_address' => $this->recipient_address,

            // Used to get the sender wallets
            'sender_public_key' => $this->sender_public_key,

            // To get the amount for single payments
            // Using `__toString` since are instances of `BigNumber`
            'amount' => $this->amount->__toString(),
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
                'recipient_address',
                'amount',
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
        return $this->belongsTo(Block::class, 'block_id');
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
        return $this->hasOne(Receipt::class, 'id', 'id');
    }

    /**
     * A transaction belongs to a recipient.
     *
     * @return Wallet
     */
    public function recipient(): Wallet
    {
        $recipient = $this->recipient_address;
        if (! is_null($recipient)) {
            return Wallet::where('address', $recipient)->firstOrFail();
        }

        $vote = Arr::get($this, 'asset.votes.0');
        if (is_null($vote)) {
            $vote = Arr::get($this, 'asset.unvotes.0');
        }

        return Wallet::where('address', $vote)->firstOrFail();
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
                        $query->when($filter['unvotes'] === true, function ($query) {
                            $query->withScope(UnvoteScope::class);
                        });
                    })
                    ->orWhere(function ($query) use ($filter) {
                        $query->when($filter['validator_registration'] === true, function ($query) {
                            $query->withScope(ValidatorRegistrationScope::class);
                        });
                    })
                    ->orWhere(function ($query) use ($filter) {
                        $query->when($filter['validator_resignation'] === true, function ($query) {
                            $query->withScope(ValidatorResignationScope::class);
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
