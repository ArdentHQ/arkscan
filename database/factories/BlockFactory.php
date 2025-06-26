<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Block;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class BlockFactory extends Factory
{
    protected $model = Block::class;

    public function definition()
    {
        return [
            'hash'                        => $this->faker->blockHash,
            'version'                     => 2,
            'timestamp'                   => 1603083256000,
            'parent_hash'                 => 1,
            'state_root'                  => 1,
            'number'                      => $this->faker->numberBetween(1, 10000),
            'transactions_count'          => $this->faker->numberBetween(1, 100),
            'amount'                      => $this->faker->numberBetween(1, 100) * 1e18,
            'fee'                         => $this->faker->numberBetween(1, 100) * 1e18,
            'gas_used'                    => $this->faker->numberBetween(1, 100),
            'reward'                      => $this->faker->numberBetween(1, 100) * 1e18,
            'payload_size'                => $this->faker->numberBetween(1, 100),
            'transactions_root'           => $this->faker->payloadHash,
            'proposer'                    => fn () => Wallet::factory()->create()->address,
            'signature'                   => $this->faker->blockSignature,
            'round'                       => 1,
            'commit_round'                => 1,
            'validator_round'             => 1,
            'validator_set'               => 1,
        ];
    }
}
