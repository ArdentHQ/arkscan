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
            'id' => $this->faker->unique()->randomNumber(),
        ];
    }
}
