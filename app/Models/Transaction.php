<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Casts\BigInteger;
use App\Models\Casts\UnixSeconds;
use App\Models\Concerns\HasEmptyScope;
use App\Models\Concerns\SearchesCaseInsensitive;
use App\Models\Scopes\DelegateRegistrationScope;
use App\Models\Scopes\DelegateResignationScope;
use App\Models\Scopes\IpfsScope;
use App\Models\Scopes\MagistrateScope;
use App\Models\Scopes\MultiPaymentScope;
use App\Models\Scopes\MultiSignatureScope;
use App\Models\Scopes\SecondSignatureScope;
use App\Models\Scopes\TimelockClaimScope;
use App\Models\Scopes\TimelockRefundScope;
use App\Models\Scopes\TimelockScope;
use App\Models\Scopes\TransferScope;
use App\Models\Scopes\VoteCombinationScope;
use App\Models\Scopes\VoteScope;
use App\Services\BigNumber;
use App\Services\VendorField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Laravel\Scout\Searchable;

/**
 * @property string $id
 * @property array|null $asset
 * @property BigNumber $amount
 * @property BigNumber $fee
 * @property int $timestamp
 * @property int $type
 * @property int $type_group
 * @property string $block_id
 * @property string|null $recipient_id
 * @property string $sender_public_key
 * @property int $block_height
 * @property resource|string|null $vendor_field
 * @property int $nonce
 * @property Wallet $sender
 * @method static \Illuminate\Database\Eloquent\Builder withScope(string $scope)
 */
final class Transaction extends Model
{
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
        'delegateRegistration'          => DelegateRegistrationScope::class,
        'delegateResignation'           => DelegateResignationScope::class,
        'ipfs'                          => IpfsScope::class,
        'multiPayment'                  => MultiPaymentScope::class,
        'multiSignature'                => MultiSignatureScope::class,
        'secondSignature'               => SecondSignatureScope::class,
        'timelockClaim'                 => TimelockClaimScope::class,
        'timelockRefund'                => TimelockRefundScope::class,
        'timelock'                      => TimelockScope::class,
        'transfer'                      => TransferScope::class,
        'vote'                          => VoteScope::class,
        'voteCombination'               => VoteCombinationScope::class,
        'magistrate'                    => MagistrateScope::class,
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
        'amount'       => BigInteger::class,
        'asset'        => 'array',
        'fee'          => BigInteger::class,
        'timestamp'    => UnixSeconds::class,
        'type_group'   => 'int',
        'type'         => 'int',
        'block_height' => 'int',
    ];

    private bool|string|null $vendorFieldContent = false;

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
            'recipient_id' => $this->recipient_id,

            // Used to get the sender wallets
            'sender_public_key' => $this->sender_public_key,

            // Used to show the transaction type
            'type'       => $this->type,
            'type_group' => $this->type_group,

            // To get the amount for single payments
            // Using `__toString` since are instances of `BigNumber`
            'amount' => $this->amount->__toString(),
            'fee'    => $this->fee->__toString(),
            // Contains the multipayments payments and vote related data
            'asset' => $this->asset,
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
                'recipient_id',
                'type',
                'type_group',
                'amount',
                'fee',
                'asset',
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
     * A transaction belongs to a recipient.
     *
    * @return Wallet
     */
    public function recipient(): Wallet
    {
        $recipientId = $this->recipient_id;
        if (!is_null($recipientId)) {
            return Wallet::firstWhere('address', $recipientId);
        }

        $votePublicKey = Arr::get($this, 'asset.votes.0');
        if (is_null($votePublicKey)) {
            $votePublicKey = Arr::get($this, 'asset.unvotes.0');
        }

        return Wallet::firstWhere('public_key', $votePublicKey);
    }

    public function scopeWithTypeFilter(Builder $query, array $filter): Builder
    {
        return $query
            ->where(function ($query) use ($filter) {
                $query->where(fn ($query) => $query->when($filter['transfers'] === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::TRANSFER)))
                    ->orWhere(fn ($query) => $query->when($filter['votes'] === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::VOTE)))
                    ->orWhere(fn ($query) => $query->when($filter['multipayments'] === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::MULTI_PAYMENT)))
                    ->orWhere(fn ($query) => $query->when($filter['others'] === true, fn ($query) => $query
                        ->where('type_group', TransactionTypeGroupEnum::MAGISTRATE)
                        ->orWhere(
                            fn ($query) => $query
                                ->where('type_group', TransactionTypeGroupEnum::CORE)
                                ->whereNotIn('type', [
                                    CoreTransactionTypeEnum::TRANSFER,
                                    CoreTransactionTypeEnum::VOTE,
                                    CoreTransactionTypeEnum::MULTI_PAYMENT,
                                ])
                        )));
            });
    }

    public function vendorField(): string|null
    {
        if (is_bool($this->vendorFieldContent)) {
            $this->vendorFieldContent = VendorField::parse($this->vendor_field);
        }

        return $this->vendorFieldContent;
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
