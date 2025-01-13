<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Cache\PriceCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Price extends Model
{
    use HasFactory;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    public $keyType = 'string';

    /**
     * The column name of the primary key.
     *
     * @var string
     */
    public $primaryKey = 'timestamp';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'timestamp' => 'datetime',
        'value'     => 'float',
    ];

    public static function getForTimestampAndCurrency(string $currency, Carbon $timestamp): float
    {
        $cache = new PriceCache();

        $timestamp->setTime(0, 0, 0, 0);
        $unixTimestamp = $timestamp->getTimestamp();
        if ($cache->hasDailyPriceForCurrency($currency, $unixTimestamp)) {
            return $cache->getDailyPriceForCurrency($currency, $unixTimestamp);
        }

        $priceData = self::where('currency', $currency)
            ->where('timestamp', $timestamp)
            ->first();

        if ($priceData === null) {
            return 0;
        }

        $cache->setDailyPriceForCurrency($currency, $unixTimestamp, $priceData->value);

        return $priceData->value;
    }
}
