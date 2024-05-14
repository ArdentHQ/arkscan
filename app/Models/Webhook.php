<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\ForgingStats\CanBeSorted;
use App\Models\Concerns\SearchesCaseInsensitive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $token
 * @property string $host
 * @property int $port
 * @property string $event
 */
final class Webhook extends Model
{
    use CanBeSorted;
    use HasFactory;
    use SearchesCaseInsensitive;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'core_webhooks';

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
    public $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'token',
        'event',
        'host',
        'port',
    ];
}
