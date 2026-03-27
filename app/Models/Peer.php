<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Peer extends Model
{
    use HasFactory;

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
