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
        public ?float $latitude,
        public ?float $longitude,
        public ?string $country,
        public ?string $city,
    ) {
    }

    public static function fromModel(Model $peer): self
    {
        return new self(
            latitude: $peer->latitude,
            longitude: $peer->longitude,
            country: $peer->country,
            city: $peer->city,
        );
    }
}
