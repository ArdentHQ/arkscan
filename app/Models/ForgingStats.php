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
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'timestamp'     => 'int',
        'public_key'    => 'string',
        'forged'        => 'bool',
    ];
}
