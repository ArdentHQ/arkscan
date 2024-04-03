<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $round
 * @property int $round_height
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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'validator_rounds';

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
        'round'        => 'int',
        'round_height' => 'int',
        'validators'   => 'array',
    ];

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
    public function validators(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
        )->shouldCache();
    }
}
