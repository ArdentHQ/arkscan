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
            'address'           => $this->faker->randomElement(json_decode(file_get_contents(base_path('tests/fixtures/addresses.json')), true)),
            'public_key'        => '03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff', // @TODO: unique public keys
            'balance'           => $this->faker->numberBetween(1, 1000) * 1e8,
            'nonce'             => $this->faker->numberBetween(1, 1000),
            'attributes'        => [
                'secondPublicKey' => $this->faker->uuid,
                'delegate'        => [
                    'username'       => $this->faker->uuid,
                    'voteBalance'    => $this->faker->numberBetween(1, 1000) * 1e8,
                    'producedBlocks' => $this->faker->numberBetween(1, 1000),
                    'missedBlocks'   => $this->faker->numberBetween(1, 1000),
                ],
            ],
        ];
    }
}
