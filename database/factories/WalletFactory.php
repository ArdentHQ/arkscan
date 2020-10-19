<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition()
    {
        return [];
    }
}
