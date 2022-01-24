<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Models\Concerns\HasEmptyScope;
use App\Models\Concerns\SearchesCaseInsensitive;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $address
 * @property string|null $public_key
 * @property BigNumber $balance
 * @property BigNumber $nonce
 * @property array $attributes
 * @method static \Illuminate\Database\Eloquent\Builder withScope(string $scope)
 */
final class Wallet extends Model
{
    use HasFactory;
    use SearchesCaseInsensitive;
    use HasEmptyScope;

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
        'balance'    => BigInteger::class,
        'nonce'      => BigInteger::class,
        'attributes' => 'array',
    ];

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
