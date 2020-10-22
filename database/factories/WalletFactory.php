<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition()
    {
        return [
            'id'                => $this->faker->uuid,
            'address'           => $this->faker->uuid,
            'public_key'        => $this->faker->uuid,
            'second_public_key' => $this->faker->uuid,
            'vote'              => $this->faker->uuid,
            'username'          => $this->faker->uuid,
            'balance'           => $this->faker->numberBetween(1, 1000) * 1e8,
            'nonce'             => $this->faker->numberBetween(1, 1000),
            'vote_balance'      => $this->faker->numberBetween(1, 1000) * 1e8,
            'produced_blocks'   => $this->faker->numberBetween(1, 1000),
            'missed_blocks'     => $this->faker->numberBetween(1, 1000),
        ];
    }
}
