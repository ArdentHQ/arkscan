<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Models\Casts\UnixSeconds;
use App\Models\Concerns\HasEmptyScope;
use App\Models\Concerns\SearchesCaseInsensitive;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

/**
 * @property string $hash
 * @property BigNumber $number
 * @property int $transactions_count
 * @property BigNumber $reward
 * @property int $timestamp
 * @property BigNumber $amount
 * @property BigNumber $fee
 * @property int $gas_used
 * @property string $proposer
 * @method static \Illuminate\Database\Eloquent\Builder withScope(string $scope)
 */
final class Block extends Model
{
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
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'explorer';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'number'                 => BigInteger::class,
        'transactions_count'     => 'int',
        'reward'                 => BigInteger::class,
        'timestamp'              => UnixSeconds::class,
        'amount'                 => BigInteger::class,
        'fee'                    => BigInteger::class,
        'gas_used'               => 'int',
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
            'hash'     => $this->hash,
            // used to get the validator
            'proposer' => $this->proposer,
            // shown on the results
            'transactions_count' => $this->transactions_count,
            // sortable attribute
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
                'hash',
                'proposer',
                'transactions_count',
                'timestamp',
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

        // @phpstan-ignore-next-line
        $self::getSearchableQuery()->searchable($chunk);
    }

    /**
     * A block has many transactions.
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'block_hash', 'hash');
    }

    /**
     * A block belongs to a validator.
     *
     * @return BelongsTo
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'proposer', 'address');
    }

    /**
     * A block has one previous block.
     *
     * @return HasOne
     */
    public function previous(): HasOne
    {
        return $this->hasOne(self::class, 'id', 'parent_hash');
    }
}
