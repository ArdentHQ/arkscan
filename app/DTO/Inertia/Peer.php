<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Models\Peer as Model;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IPeer')]
class Peer extends Data
{
    public function __construct(
        public string $ip,
        public int $port,
        public ?float $latitude,
        public ?float $longitude,
    ) {
    }

    public static function fromModel(Model $peer): self
    {
        return new self(
            ip: $peer->ip,
            port: $peer->port,
            latitude: $peer->latitude,
            longitude: $peer->longitude,
        );
    }
}
