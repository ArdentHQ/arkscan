<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Round;
use Illuminate\Database\Eloquent\Factories\Factory;

final class RoundFactory extends Factory
{
    protected $model = Round::class;

    public function definition()
    {
        return [
            'id'         => $this->faker->uuid,
            'public_key' => $this->faker->publicKey,
            'balance'    => $this->faker->numberBetween(1, 1000) * 1e8,
            'round'      => $this->faker->numberBetween(1, 10000),
        ];
    }
}
