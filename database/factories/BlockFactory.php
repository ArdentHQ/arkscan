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
            'id'                     => $this->faker->blockId,
            'version'                => 2,
            'timestamp'              => 1603083256000,
            'previous_block'         => 1,
            'height'                 => $this->faker->numberBetween(1, 10000),
            'number_of_transactions' => $this->faker->numberBetween(1, 100),
            'total_amount'           => $this->faker->numberBetween(1, 100) * 1e18,
            'total_fee'              => $this->faker->numberBetween(1, 100) * 1e18,
            'reward'                 => $this->faker->numberBetween(1, 100) * 1e18,
            'payload_length'         => $this->faker->numberBetween(1, 100),
            'payload_hash'           => $this->faker->payloadHash,
            'generator_address'      => fn () => Wallet::factory()->create()->address,
            'block_signature'        => $this->faker->blockSignature,
        ];
    }
}
