<?php

declare(strict_types=1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $address
 * @property string $public_key
 * @property int $balance
 * @property int $nonce
 * @property array $attributes
 */
final class Wallet extends Model
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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'balance'    => 'int',
        'nonce'      => 'int',
        'attributes' => 'array',
    ];

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
     * Get the route key for the model.
     *
     * @return string
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
