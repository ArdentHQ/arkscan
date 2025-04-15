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
            'transaction_hash'                        => $this->faker->transactionHash,
            'status'                                  => $this->faker->boolean,
            'block_number'                            => $this->faker->numberBetween(1, 10000),
            'gas_used'                                => $this->faker->numberBetween(1, 100),
            'gas_refunded'                            => $this->faker->numberBetween(1, 100),
            'contract_address'                        => fn () => Wallet::factory()->create()->address,
            'logs'                                    => [],
            'output'                                  => null,
        ];
    }

    public function withTransaction()
    {
        $transaction = Transaction::factory()->create();

        return $this->state(fn (array $attributes) => [
            'transaction_hash' => $transaction->hash,
        ]);
    }
}
