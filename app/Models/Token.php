<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property string $address
 * @property string $symbol
 * @property string $name
 * @property string $symbolNormalized
 * @property string $symbolFull
 * @property string $nameNormalized
 * @property int $decimals
 * @property BigNumber $total_supply
 * @property string $deployment_hash
 */
final class Token extends Model
{
    use HasFactory;

    private const MAX_NAME_LENGTH = 20;

    private const MAX_SYMBOL_LENGTH = 5;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    public $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $connection = 'explorer';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'address';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_supply' => BigInteger::class,
    ];

    public function getNameNormalizedAttribute(): string
    {
        return self::normalizeString($this->name, self::MAX_NAME_LENGTH);
    }

    public function getSymbolNormalizedAttribute(): string
    {
        return self::normalizeString($this->symbol, self::MAX_SYMBOL_LENGTH, '…');
    }

    public function getSymbolFullAttribute(): string
    {
        return self::normalizeString($this->symbol);
    }

    private static function normalizeString(string $value, ?int $maxLength = null, ?string $suffix = null): string
    {
        if ($maxLength === null) {
            return Str::trim($value);
        }

        if (strlen($value) <= $maxLength) {
            return $value;
        }

        if (! Str::isAscii($value) && function_exists('mb_strimwidth')) {
            $value = mb_strimwidth($value, 0, $maxLength, '', 'UTF-8');
        } else {
            $value = substr($value, 0, $maxLength);
        }

        if ($suffix !== null) {
            return Str::trim($value).$suffix;
        }

        return Str::trim($value);
    }
}
