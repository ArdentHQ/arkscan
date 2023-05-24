<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exchange>
 */
class ExchangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'          => fake()->unique()->word(),
            'url'           => fake()->url(),
            'is_exchange'   => fake()->boolean(),
            'is_aggregator' => fake()->boolean(),
            'btc'           => fake()->boolean(),
            'eth'           => fake()->boolean(),
            'stablecoins'   => fake()->boolean(),
            'other'         => fake()->boolean(),
            'icon'          => fake()->word(),
            'coingecko_id'  => fake()->optional()->word(),
            'price'         => fake()->optional()->randomNumber(),
            'volume'        => fake()->optional()->randomNumber(),
        ];
    }
}
