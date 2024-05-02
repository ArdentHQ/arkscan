<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\ForgingStats\CanBeSorted;
use App\Models\Concerns\SearchesCaseInsensitive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $missed_height
 * @property int $timestamp
 * @property string $public_key
 * @property bool $forged
 * @property int $count (only available when sorting validators by missed blocks)
 */
final class ForgingStats extends Model
{
    use CanBeSorted;
    use HasFactory;
    use SearchesCaseInsensitive;

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
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'missed_height' => 'int',
        'timestamp'     => 'int',
        'public_key'    => 'string',
        'forged'        => 'bool',
    ];

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
     * Get all missed forging stats.
     *
     * @return Builder
     */
    public function scopeMissed(Builder $query): Builder
    {
        return $query->whereNot('missed_height', null);
    }

    // /**
    //  * Get the current connection name for the model.
    //  *
    //  * @return string
    //  */
    // public function getConnectionName()
    // {
    //     return config('database.default');
    // }
}
