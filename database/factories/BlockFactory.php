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
            'height' => $this->faker->numberBetween(1, 10000),
        ];
    }
}
