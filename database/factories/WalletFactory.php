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
            'address'           => $this->faker->randomElement([
                'DRgF3PvzeGWndQjET7dZsSmnrc6uAy23ES',
                'D8vwEEvKgMPVvvK2Zwzyb5uHzRadurCcKq',
                'DL6wmfnA2acPLpBjKS4zPGsSwxkTtGANsK',
                'DNPBUxxGQUKDPX3XKUXa3pc4GK8yz7L97T',
                'D6Z26L69gdk9qYmTv5uzk3uGepigtHY4ax',
                'DDU4aLrxw9VYJzrMTYtRAyDM9fKsqciiYd',
                'DJic2dfPgxaGJeme1tQPpLuvkJMKo6PDfP',
                'D7u9gSS3KsykEoRys7DxsRNwHjpYoG8mqS',
                'DNjuJEDQkhrJ7cA9FZ2iVXt5anYiM8Jtc9',
                'DRW3wNMA4ijPfm7KA3XtupDNb5Hb8kL4AE',
            ]),
            'public_key'        => $this->faker->uuid,
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
