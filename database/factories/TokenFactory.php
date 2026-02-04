<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Token;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TokenFactory extends Factory
{
    protected $model = Token::class;

    public function definition()
    {
        return [
            'address'         => $this->faker->address,
            'symbol'          => strtoupper($this->faker->lexify('???')),
            'name'            => $this->faker->company . ' Token',
            'decimals'        => $this->faker->numberBetween(6, 18),
            'total_supply'    => (string) BigNumber::new($this->faker->numberBetween(1_000_000, 1_000_000_000))->multipliedBy(1e18),
            'deployment_hash' => $this->faker->transactionHash,
        ];
    }
}
