<?php

declare(strict_types=1);

namespace App\Models;

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
     * Get the human readable representation of the balance.
     *
     * @return float
     */
    public function getFormattedBalanceAttribute(): float
    {
        return $this->balance / 1e8;
    }

    /**
     * Get the human readable representation of the vote balance.
     *
     * @return float
     */
    public function getFormattedVoteBalanceAttribute(): float
    {
        return $this->vote_balance / 1e8;
    }

    /**
     * Find a wallet by its address.
     *
     * @param string $value
     *
     * @return Wallet
     */
    public static function findByAddress(string $value): self
    {
        return static::whereAddress($value)->firstOrFail();
    }

    /**
     * Find a wallet by its public-key.
     *
     * @param string $value
     *
     * @return Wallet
     */
    public static function findByPublicKey(string $value): self
    {
        return static::wherePublicKey($value)->firstOrFail();
    }

    /**
     * Find a wallet by its username.
     *
     * @param string $value
     *
     * @return Wallet
     */
    public static function findByUsername(string $value): self
    {
        return static::whereUsername($value)->firstOrFail();
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
