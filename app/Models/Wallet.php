<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Models\Concerns\HasEmptyScope;
use App\Models\Concerns\SearchesCaseInsensitive;
use App\Models\Concerns\Wallet\CanBeSorted;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

/**
 * @property string $address
 * @property string|null $public_key
 * @property BigNumber $balance
 * @property BigNumber $nonce
 * @property array $attributes
 * @property int $updated_at
 * @property string $timestamp (only available when indexed by scout)
 * @property int $missed_blocks (only available when sorting validators by missed blocks)
 * @method static \Illuminate\Database\Eloquent\Builder withScope(string $scope)
 */
final class Wallet extends Model
{
    use CanBeSorted;
    use HasFactory;
    use SearchesCaseInsensitive;
    use HasEmptyScope;
    use Searchable;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    public $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The column name of the primary key.
     *
     * @var string
     */
    protected $primaryKey = 'address';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance'    => BigInteger::class,
        'nonce'      => BigInteger::class,
        'attributes' => 'array',
    ];

    /**
     * Get the value used to index the model.
     */
    public function getScoutKey(): mixed
    {
        return $this->address;
    }

    /**
     * Get the key name used to index the model.
     */
    public function getScoutKeyName(): mixed
    {
        return 'address';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'address'   => $this->address,
            'balance'   => $this->balance->__toString(),
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
                'wallets.address',
                'wallets.attributes',
                'wallets.balance',
                DB::raw('wallets.updated_at as timestamp'),
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
     * A wallet has many sent transactions.
     *
     * @return HasMany
     */
    public function sentTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'sender_public_key', 'public_key');
    }

    /**
     * A wallet has many received transactions.
     *
     * @return HasMany
     */
    public function receivedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'recipient_address', 'address');
    }

    /**
     * A wallet has many blocks if it is a validator.
     *
     * @return HasMany
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'generator_address', 'address');
    }

    public function voteBalance(): BigNumber
    {
        $voteBalance = Arr::get($this->attributes, 'validatorVoteBalance');
        if ($voteBalance === null) {
            return BigNumber::new('0');
        }

        return $this->attributes['validatorVoteBalance'];
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getRouteKeyName()
    {
        return 'address';
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
