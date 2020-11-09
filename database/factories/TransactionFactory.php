<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'id'                => $this->faker->uuid,
            'block_id'          => fn () => Block::factory(),
            'block_height'      => $this->faker->numberBetween(1, 10000),
            'type'              => $this->faker->numberBetween(1, 100),
            'type_group'        => $this->faker->numberBetween(1, 100),
            'sender_public_key' => fn () => Wallet::factory()->create()->public_key,
            'recipient_id'      => fn () => Wallet::factory()->create()->address,
            'timestamp'         => 112982056,
            'fee'               => $this->faker->numberBetween(1, 100) * 1e8,
            'amount'            => $this->faker->numberBetween(1, 100) * 1e8,
            'asset'             => [
                'ipfs' => 'QmXrvSZaDr8vjLUB9b7xz26S3kpk3S3bSc8SUyZmNPvmVo',
            ],
        ];
    }
}
