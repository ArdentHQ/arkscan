<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $transaction_hash
 * @property bool $status
 * @property BigNumber $block_number
 * @property BigNumber $gas_used
 * @property BigNumber $gas_refunded
 * @property string|null $contract_address
 * @property array $logs
 * @property string $output
 */
final class Receipt extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    public $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'explorer';

    /**
     * The column name of the primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_hash'           => 'string',
        'status'      => 'bool',
        'block_number' => BigInteger::class,
        'gas_used'     => BigInteger::class,
        'gas_refunded' => BigInteger::class,
        'logs'         => 'array',
        'output'       => 'string',
    ];

    /**
     * A wallet has many blocks if it is a validator.
     *
     * @return HasOne
     */
    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'hash', 'transaction_hash');
    }
}
