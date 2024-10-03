<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

final class StateFactory extends Factory
{
    protected $model = State::class;

    public function definition()
    {
        return [
            'id'     => 1,
            'height' => $this->faker->numberBetween(1, 10000),
            'supply' => $this->faker->numberBetween(1, 1000000) * 1e18,
        ];
    }
}
