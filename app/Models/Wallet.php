<?php

declare(strict_types=1);

namespace App\Models;

use App\Facades\Network;
use App\Services\NumberFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property float $balance
 * @property float $vote_balance
 */
final class Wallet extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * A wallet has many sent transactions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'sender_public_key', 'public_key');
    }

    /**
     * A wallet has many received transactions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'recipient_id', 'address');
    }

    /**
     * A wallet has many blocks if it is a delegate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'generator_public_key', 'public_key');
    }

    /**
     * Scope a query to only include transactions by the recipient.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $publicKey
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVote($query, $publicKey)
    {
        return $query->where('vote', $publicKey);
    }

    /**
     * Scope a query to sort wallets by balance.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWealthy($query)
    {
        return $query->orderBy('balance', 'desc');
    }

    /**
     * Get the human readable representation of the balance.
     *
     * @return string
     */
    public function getFormattedBalanceAttribute(): string
    {
        return NumberFormatter::currency($this->balance / 1e8, Network::currency());
    }

    /**
     * Get the human readable representation of the vote balance.
     *
     * @return string
     */
    public function getFormattedVoteBalanceAttribute(): string
    {
        return NumberFormatter::currency($this->vote_balance / 1e8, Network::currency());
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
