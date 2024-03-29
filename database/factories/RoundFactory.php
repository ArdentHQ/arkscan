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
            'validators'   => $this->faker->validators,
            'round'        => $this->faker->numberBetween(1, 10000),
            'round_height' => $this->faker->numberBetween(1, 10000),
        ];
    }
}
