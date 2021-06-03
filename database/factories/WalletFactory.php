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
        $wallet = $this->faker->wallet;

        return [
            'id'                => $this->faker->uuid,
            'address'           => $wallet['address'],
            'public_key'        => $wallet['publicKey'],
            'balance'           => $this->faker->numberBetween(1, 1000) * 1e8,
            'nonce'             => $this->faker->numberBetween(1, 1000),
            'attributes'        => [
                'secondPublicKey' => $this->faker->publicKey,
                'delegate'        => [
                    'username'       => $this->faker->userName,
                    'voteBalance'    => $this->faker->numberBetween(1, 1000) * 1e8,
                    'producedBlocks' => $this->faker->numberBetween(1, 1000),
                    'missedBlocks'   => $this->faker->numberBetween(1, 1000),
                ],
            ],
        ];
    }

    public function activeDelegate()
    {
        return $this->state(function () {
            return [
                'attributes'        => [
                    'delegate'        => [
                        'rank'           => $this->faker->numberBetween(1, 51),
                        'username'       => $this->faker->userName,
                        'voteBalance'    => $this->faker->numberBetween(1, 1000) * 1e8,
                        'producedBlocks' => $this->faker->numberBetween(1, 1000),
                        'missedBlocks'   => $this->faker->numberBetween(1, 1000),
                    ],
                ],
            ];
        });
    }

    public function standbyDelegate()
    {
        return $this->state(function () {
            return [
                'attributes'        => [
                    'delegate'        => [
                        'resigned'       => true,
                        'rank'           => $this->faker->numberBetween(52, 102),
                        'username'       => $this->faker->userName,
                        'voteBalance'    => $this->faker->numberBetween(1, 100) * 1e8,
                        'producedBlocks' => $this->faker->numberBetween(1, 1000),
                        'missedBlocks'   => $this->faker->numberBetween(1, 1000),
                    ],
                ],
            ];
        });
    }
}
