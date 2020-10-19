<?php

declare(strict_types=1);

namespace App\Models;

use ArkEcosystem\Crypto\Configuration\Network;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property float $totalAmount
 * @property float $totalFee
 * @property float $reward
 */
final class Block extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

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

    /**
     * Get the human readable representation of the timestamp.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function getTimestampCarbonAttribute(): Carbon
    {
        return Carbon::parse(Network::get()->epoch())
            ->addSeconds($this->attributes['timestamp']);
    }

    /**
     * Get the human readable representation of the total.
     *
     * @return float
     */
    public function getFormattedTotalAttribute(): float
    {
        return $this->totalAmount / 1e8;
    }

    /**
     * Get the human readable representation of the fee.
     *
     * @return float
     */
    public function getFormattedFeeAttribute(): float
    {
        return $this->totalFee / 1e8;
    }

    /**
     * Get the human readable representation of the reward.
     *
     * @return float
     */
    public function getFormattedRewardAttribute(): float
    {
        return $this->reward / 1e8;
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
