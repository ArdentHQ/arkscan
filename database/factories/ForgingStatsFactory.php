<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ForgingStats;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ForgingStatsFactory extends Factory
{
    protected $model = ForgingStats::class;

    public function definition()
    {
        return [
            'timestamp'     => $this->faker->dateTimeBetween('-1 year', 'now')->getTimestamp(),
            'address'       => fn () => Wallet::factory()->create()->address,
            'forged'        => $this->faker->boolean(),
            'missed_height' => $this->faker->numberBetween(1, 10000),
        ];
    }
}
