<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\SearchesCaseInsensitive;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $timestamp
 * @property string $public_key
 * @property bool $forged
 */
final class ForgingStats extends Model
{
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
        'timestamp'     => 'int',
        'missed_height' => 'int',
        'public_key'    => 'string',
        'forged'        => 'bool',
    ];
}
