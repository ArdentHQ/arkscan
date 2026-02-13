<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Token;
use App\Models\TokenHolder;
use App\Models\Wallet;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TokenHolderFactory extends Factory
{
    protected $model = TokenHolder::class;

    public function definition()
    {
        return [
            'token_address'    => fn () => Token::factory()->create()->address,
            'address'          => fn () => Wallet::factory()->create()->address,
            'balance'          => (string) BigNumber::new($this->faker->numberBetween(1, 1000))->multipliedBy(1e18),
        ];
    }
}
