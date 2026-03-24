<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Peer extends Model
{
    protected $fillable = [
        'ip',
        'port',
        'latitude',
        'longitude',
        'country',
        'city',
    ];

    protected function casts(): array
    {
        return [
            'port'      => 'integer',
            'latitude'  => 'float',
            'longitude' => 'float',
        ];
    }
}
