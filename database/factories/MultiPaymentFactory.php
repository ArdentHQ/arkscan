<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MultiPayment;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class MultiPaymentFactory extends Factory
{
    protected $model = MultiPayment::class;

    public function definition()
    {
        $wallet = Wallet::factory()->create();

        return [
            'hash'      => $this->faker->transactionHash,
            'log_index' => 1,
            'from'      => fn () => $wallet->address,
            'to'        => fn () => $wallet->address,
            'amount'    => $this->faker->numberBetween(1, 100) * 1e18,
            'success'   => $this->faker->boolean,
        ];
    }
}
