<?php

declare(strict_types=1);

namespace  App\Models;

use ArkEcosystem\Crypto\Configuration\Network;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property float $fee
 * @property float $amount
 */
final class Transaction extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['serialized'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = ['asset' => 'array'];

    /**
     * A transaction belongs to a block.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    /**
     * A transaction belongs to a sender.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'sender_public_key', 'public_key');
    }

    /**
     * A transaction belongs to a recipient.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'recipient_id', 'address');
    }

    /**
     * Scope a query to only include transactions by the sender.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $publicKey
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSendBy($query, $publicKey)
    {
        return $query->where('sender_public_key', $publicKey);
    }

    /**
     * Scope a query to only include transactions by the recipient.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $address
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReceivedBy($query, $address)
    {
        return $query->where('recipient_id', $address);
    }

    /**
     * Get the human readable representation of the timestamp.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function getTimestampCarbonAttribute(): Carbon
    {
        return Carbon::parse(Network::get()->epoch())->addSeconds($this->attributes['timestamp']);
    }

    /**
     * Get the human readable representation of the vendor field.
     *
     * @return string
     */
    public function getVendorFieldAttribute(): ?string
    {
        $vendorFieldHex = $this->attributes['vendor_field'];

        if (is_null($vendorFieldHex)) {
            return null;
        }

        return hex2bin(bin2hex(stream_get_contents($vendorFieldHex)));
    }

    /**
     * Get the human readable representation of the fee.
     *
     * @return float
     */
    public function getFormattedFeeAttribute(): float
    {
        return $this->fee / 1e8;
    }

    /**
     * Get the human readable representation of the amount.
     *
     * @return float
     */
    public function getFormattedAmountAttribute(): float
    {
        return $this->amount / 1e8;
    }

    /**
     * Find a wallet by its address.
     *
     * @param string $value
     *
     * @return Wallet
     */
    public static function findById(string $value): self
    {
        return static::whereId($value)->firstOrFail();
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
