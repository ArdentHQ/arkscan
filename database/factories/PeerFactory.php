<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Peer;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PeerFactory extends Factory
{
    protected $model = Peer::class;

    public function definition(): array
    {
        return [
            'ip'        => $this->faker->unique()->ipv4(),
            'port'      => 4000,
            'latitude'  => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'country'   => $this->faker->country(),
            'city'      => $this->faker->city(),
        ];
    }
}
