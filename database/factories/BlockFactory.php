<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Block;
use Illuminate\Database\Eloquent\Factories\Factory;

final class BlockFactory extends Factory
{
    protected $model = Block::class;

    public function definition()
    {
        return [
            'id'                   => $this->faker->unique()->randomNumber(),
            'previous_block'       => 1,
            'height'               => $this->faker->numberBetween(1, 10000),
            'generator_public_key' => $this->faker->word,
            'timestamp'            => 112982056,
            'totalAmount'          => $this->faker->numberBetween(1, 100) * 1e8,
            'totalFee'             => $this->faker->numberBetween(1, 100) * 1e8,
            'reward'               => $this->faker->numberBetween(1, 100) * 1e8,
        ];
    }
}
