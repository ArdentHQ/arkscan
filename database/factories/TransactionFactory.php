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
            'id'                => $this->faker->unique()->randomNumber(),
            'block_id'          => Block::factory(),
            'sender_public_key' => $this->faker->word,
            'recipient_id'      => $this->faker->word,
            'timestamp'         => 112982056,
            'fee'               => $this->faker->numberBetween(1, 100) * 1e8,
            'amount'            => $this->faker->numberBetween(1, 100) * 1e8,
        ];
    }
}
