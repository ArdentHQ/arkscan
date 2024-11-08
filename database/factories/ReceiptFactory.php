<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ReceiptFactory extends Factory
{
    protected $model = Receipt::class;

    public function definition()
    {
        return [
            'id' => $this->faker->transactionId,
            'success' => $this->faker->boolean,
            'block_height' => $this->faker->numberBetween(1, 10000),
            'gas_used' => $this->faker->numberBetween(1, 100),
            'gas_refunded' => $this->faker->numberBetween(1, 100),
            'deployed_contract_address' => fn () => Wallet::factory()->create()->address,
            'logs' => [],
            'output' => null,
        ];
    }

    public function withTransaction()
    {
        $transaction = Transaction::factory()->create();

        return $this->state(fn (array $attributes) => [
            'id' => $transaction->id,
        ]);
    }
}
