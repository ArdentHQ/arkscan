<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $round
 * @property int $round_height
 * @property string $public_key
 * @property string[] $validators
 * @property BigNumber $balance
 */
final class Round extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'round';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance'      => BigInteger::class,
        'public_key'   => 'string',
        'round'        => 'int',
        'round_height' => 'int',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'validator_rounds';

    /**
     * A round slot belongs to a validator.
     *
     * @return BelongsTo
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'public_key', 'public_key');
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

    /**
     * @return Attribute
     *
     * @phpstan-ignore-next-line
     */
    private function validators(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
        )->shouldCache();
    }
}
