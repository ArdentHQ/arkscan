<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Models\Concerns\HasEmptyScope;
use App\Models\Concerns\SearchesCaseInsensitive;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

/**
 * @property string $address
 * @property string|null $public_key
 * @property BigNumber $balance
 * @property BigNumber $nonce
 * @property array $attributes
 * @property string $delegate_username (only available when indexed by scout)
 * @property string $timestamp (only available when indexed by scout)
 * @method static \Illuminate\Database\Eloquent\Builder withScope(string $scope)
 */
final class Wallet extends Model
{
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
            'username'  => $this->delegate_username,
            'balance'   => $this->balance->__toString(),
            'timestamp' => $this->timestamp ?? 0,
        ];
    }

    /**
     * @return Builder<self>
     */
    public static function getSearchableQuery(): Builder
    {
        $self = new static();

        // Consider that the original `vendor/laravel/scout/src/Searchable.php@makeAllSearchable`
        // method contains more logic to see stuff like if should use soft delete
        // and stuff like that but we don't need it here.
        return $self->newQuery()
            ->select([
                DB::raw("wallets.attributes->'delegate'->>'username' AS delegate_username"),
                'wallets.address',
                'wallets.attributes',
                'wallets.balance',
                DB::raw('(SELECT MIN(transactions.timestamp) FROM transactions WHERE transactions.recipient_id = wallets.address) as timestamp'),
            ])
            ->when(true, function ($query) use ($self) {
                $self->makeAllSearchableUsing($query);
            });
    }

    /**
     * Overrides `vendor/laravel/scout/src/Searchable.php@makeAllSearchable`
     * to add a custom property and optimize the query.
     *
     * @param  int  $chunk
     * @return void
     */
    public static function makeAllSearchable($chunk = null)
    {
        $self = new static();

        // Consider that the original `vendor/laravel/scout/src/Searchable.php@makeAllSearchable`
        // method contains more logic to see stuff like if should use soft delete
        // and stuff like that but we don't need it here.
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
        return $this->hasMany(Transaction::class, 'recipient_id', 'address');
    }

    /**
     * A wallet has many blocks if it is a delegate.
     *
     * @return HasMany
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'generator_public_key', 'public_key');
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
