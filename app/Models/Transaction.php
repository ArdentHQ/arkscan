<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Models\Casts\UnixSeconds;
use App\Models\Concerns\HasEmptyScope;
use App\Models\Concerns\SearchesCaseInsensitive;
use App\Models\Concerns\Transaction\CanBeSorted;
use App\Models\Scopes\MultiPaymentScope;
use App\Models\Scopes\MultiSignatureScope;
use App\Models\Scopes\TransferScope;
use App\Models\Scopes\UsernameRegistrationScope;
use App\Models\Scopes\UsernameResignationScope;
use App\Models\Scopes\ValidatorRegistrationScope;
use App\Models\Scopes\ValidatorResignationScope;
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
 * @property int $timestamp
 * @property int $type
 * @property int $type_group
 * @property string $block_id
 * @property string|null $recipient_address
 * @property string $sender_public_key
 * @property int $block_height
 * @property resource|string|null $vendor_field
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
        'validatorRegistration'         => ValidatorRegistrationScope::class,
        'validatorResignation'          => ValidatorResignationScope::class,
        'multiPayment'                  => MultiPaymentScope::class,
        'multiSignature'                => MultiSignatureScope::class,
        'usernameRegistration'          => UsernameRegistrationScope::class,
        'usernameResignation'           => UsernameResignationScope::class,
        'transfer'                      => TransferScope::class,
        'vote'                          => VoteScope::class,
        'voteCombination'               => VoteCombinationScope::class,
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
            'recipient_address' => $this->recipient_address,

            // Used to get the sender wallets
            'sender_public_key' => $this->sender_public_key,

            // To get the amount for single payments
            // Using `__toString` since are instances of `BigNumber`
            'amount' => $this->amount->__toString(),
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
        if (! is_null($recipientId)) {
            return Wallet::where('address', $recipientId)->firstOrFail();
        }

        $vote = Arr::get($this, 'asset.votes.0');
        if (is_null($vote)) {
            $vote = Arr::get($this, 'asset.unvotes.0');
        }

        return Wallet::where('address', $vote)->firstOrFail();
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
