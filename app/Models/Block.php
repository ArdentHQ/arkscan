<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Services\BigNumber;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
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
 */
final class Block extends Model
{
    // use Cachable;
    use HasFactory;

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

    // /**
    //  * The relations to eager load on every query.
    //  *
    //  * @var array
    //  */
    // protected $with = ['delegate'];

    /**
     * A block has many transactions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'block_id', 'id');
    }

    /**
     * A block belongs to a delegate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delegate(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'generator_public_key', 'public_key');
    }

    /**
     * A block has one previous block.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function previous(): HasOne
    {
        return $this->hasOne(self::class, 'id', 'previous_block');
    }

    /**
     * Scope a query to sort blocks by their height, new to old.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestByHeight($query)
    {
        return $query->orderBy('height', 'desc');
    }

    /**
     * Scope a query to only include blocks by the generator.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $publicKey
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGenerator($query, $publicKey)
    {
        return $query->where('generator_public_key', $publicKey);
    }

    public static function current(): self
    {
        return static::orderBy('height', 'desc')->firstOrFail();
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
