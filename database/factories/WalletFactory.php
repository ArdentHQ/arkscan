<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Facades\Network;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition()
    {
        $wallet = $this->faker->wallet;

        return [
            'id'         => $this->faker->uuid,
            'address'    => $wallet['address'],
            'public_key' => $wallet['publicKey'],
            'balance'    => $this->faker->numberBetween(1, 1000) * 1e18,
            'nonce'      => $this->faker->numberBetween(1, 1000),
            'attributes' => [
                'secondPublicKey'         => $this->faker->publicKey,
                'validatorVoteBalance'    => $this->faker->numberBetween(1, 1000) * 1e18,
                'validatorProducedBlocks' => $this->faker->numberBetween(1, 1000),
                'validatorMissedBlocks'   => $this->faker->numberBetween(1, 1000),
            ],
            'updated_at' => $this->faker->numberBetween(0, 1000),
        ];
    }

    public function activeValidator()
    {
        return $this->state(function () {
            return [
                'attributes' => [
                    'validatorPublicKey'      => $this->faker->publicKey,
                    'validatorRank'           => $this->faker->numberBetween(1, Network::validatorCount()),
                    'validatorApproval'       => $this->faker->randomFloat(2, 0, 2),
                    'validatorPublicKey'      => $this->faker->publicKey,
                    'validatorForgedFees'     => $this->faker->numberBetween(1, 100) * 1e18,
                    'validatorForgedTotal'    => $this->faker->numberBetween(1, 100) * 1e18,
                    'validatorVoteBalance'    => $this->faker->numberBetween(1, 100) * 1e18,
                    'validatorForgedRewards'  => $this->faker->numberBetween(1, 100) * 1e18,
                    'validatorProducedBlocks' => $this->faker->numberBetween(1, 1000),
                ],
            ];
        });
    }

    public function standbyValidator(bool $isResigned = true)
    {
        return $this->state(function () use ($isResigned) {
            if ($isResigned) {
                return [
                    'attributes' => [
                        'validatorResigned'    => true,
                        'validatorPublicKey'   => $this->faker->publicKey,
                        'validatorVoteBalance' => $this->faker->numberBetween(1, 100) * 1e18,
                    ],
                ];
            }

            return [
                'attributes' => [
                    'validatorRank'           => $this->faker->numberBetween(Network::validatorCount() + 1, (Network::validatorCount() * 2) - 1),
                    'validatorApproval'       => $this->faker->randomFloat(2, 0, 2),
                    'validatorPublicKey'      => $this->faker->publicKey,
                    'validatorForgedFees'     => $this->faker->numberBetween(1, 100) * 1e18,
                    'validatorForgedTotal'    => $this->faker->numberBetween(1, 100) * 1e18,
                    'validatorVoteBalance'    => $this->faker->numberBetween(1, 100) * 1e18,
                    'validatorForgedRewards'  => $this->faker->numberBetween(1, 100) * 1e18,
                    'validatorProducedBlocks' => $this->faker->numberBetween(1, 1000),
                ],
            ];
        });
    }

    public function multiSignature()
    {
        return $this->state(function () {
            return [
                'attributes' => [
                    'multiSignature' => [
                        'min'        => 2,
                        'publicKeys' => [
                            '022a40ea35d53eedf0341ffa17574fca844d69665ce35f224e9a6b1385575044fd',
                            '037fde73baaa48eb75c013fe9ff52a74a096d48b9978351bdcb5b72331ca37487c',
                        ],
                    ],
                ],
            ];
        });
    }
}
