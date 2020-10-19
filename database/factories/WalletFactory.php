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
            'id'         => $this->faker->unique()->randomNumber(),
            'address'    => $this->faker->unique()->word,
            'public_key' => $this->faker->unique()->word,
            'username'   => $this->faker->unique()->word,
        ];
    }
}
