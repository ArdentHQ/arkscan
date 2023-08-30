<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\SearchesCaseInsensitive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $missed_height
 * @property int $timestamp
 * @property string $public_key
 * @property bool $forged
 */
final class ForgingStats extends Model
{
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
     * A round slot belongs to a delegate.
     *
     * @return BelongsTo
     */
    public function delegate(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'public_key', 'public_key');
    }

    /**
     * A round slot belongs to a delegate.
     *
     * @return BelongsTo
     */
    public function scopeMissed(Builder $query): Builder
    {
        return $query->where('missed_height', null);
    }
}
