<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Models\Concerns\HasEmptyScope;
use App\Models\Concerns\SearchesCaseInsensitive;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property BigNumber $height
 * @property int $number_of_transactions
 * @property BigNumber $reward
 * @property int $timestamp
 * @property BigNumber $total_amount
 * @property BigNumber $total_fee
 * @property string $generator_public_key
 * @method static \Illuminate\Database\Eloquent\Builder withScope(string $scope)
 */
final class Block extends Model
{
    use HasFactory;
    use SearchesCaseInsensitive;
    use HasEmptyScope;

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'height'                 => BigInteger::class,
        'number_of_transactions' => 'int',
        'reward'                 => BigInteger::class,
        'timestamp'              => 'int',
        'total_amount'           => BigInteger::class,
        'total_fee'              => BigInteger::class,
    ];

    /**
     * A block has many transactions.
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'block_id', 'id');
    }

    /**
     * A block belongs to a delegate.
     *
     * @return BelongsTo
     */
    public function delegate(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'generator_public_key', 'public_key');
    }

    /**
     * A block has one previous block.
     *
     * @return HasOne
     */
    public function previous(): HasOne
    {
        return $this->hasOne(self::class, 'id', 'previous_block');
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
