<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property bool $success
 * @property BigNumber $block_height
 * @property BigNumber $gas_used
 * @property BigNumber $gas_refunded
 * @property resource|string|null $deployed_contract_address
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
        'id'           => 'string',
        'success'      => 'bool',
        'block_height' => BigInteger::class,
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
        return $this->hasOne(Transaction::class, 'id', 'id');
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
