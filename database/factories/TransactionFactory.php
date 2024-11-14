<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Block;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        $wallet = Wallet::factory()->create();

        return [
            'id'                => $this->faker->transactionId,
            'block_id'          => fn () => Block::factory(),
            'block_height'      => $this->faker->numberBetween(1, 10000),
            'sender_public_key' => fn () => $wallet->public_key,
            'recipient_id'      => fn () => $wallet->address,
            'timestamp'         => 1603083256000,
            'gas_price'         => $this->faker->numberBetween(1, 100),
            'amount'            => $this->faker->numberBetween(1, 100) * 1e18,
            'nonce'             => 1,
        ];
    }

    public function withReceipt(int $gasUsed = 21000): Factory
    {
        return $this->has(Receipt::factory()->state(fn () => ['gas_used' => $gasUsed]));
    }
}
