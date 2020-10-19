<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\NumberFormatter;
use ArkEcosystem\Crypto\Configuration\Network;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
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
     * Scope a query to sort transactions by their forging time, new to old.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestByTimestamp($query)
    {
        return $query->orderBy('timestamp', 'desc');
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
    // @codeCoverageIgnoreStart
    public function getVendorFieldAttribute(): ?string
    {
        $vendorFieldHex = Arr::get($this->attributes, 'vendor_field_hex');

        if (is_null($vendorFieldHex)) {
            return null;
        }

        return hex2bin(bin2hex(stream_get_contents($vendorFieldHex)));
    }

    // @codeCoverageIgnoreEnd

    /**
     * Get the human readable representation of the fee.
     *
     * @return string
     */
    public function getFormattedFeeAttribute(): string
    {
        return NumberFormatter::currency($this->fee / 1e8, \App\Facades\Network::currency());
    }

    /**
     * Get the human readable representation of the amount.
     *
     * @return string
     */
    public function getFormattedAmountAttribute(): string
    {
        return NumberFormatter::currency($this->amount / 1e8, \App\Facades\Network::currency());
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
