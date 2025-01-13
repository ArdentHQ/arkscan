<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Price;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition()
    {
        return [
            'timestamp' => $this->faker->dateTimeBetween('-1 year', 'now')->getTimestamp(),
            'currency'  => $this->faker->publicKey,
            'value'     => $this->faker->randomFloat(max: 10),
        ];
    }
}
