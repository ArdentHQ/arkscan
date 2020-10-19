<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Block;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'id'       => $this->faker->unique()->randomNumber(),
            'block_id' => Block::factory(),
        ];
    }
}
